<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Service;

use App\User as AppUser;
use Orq\DddBase\ModelFactory;
use Orq\DddBase\DomainException;
use Illuminate\Support\Facades\Log;
use App\MicroGroup\Domain\Model\User;
use Orq\Laravel\YaCommerce\Payment\WxPay;
use Orq\Laravel\YaCommerce\Events\SavedOrder;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;
use Orq\Laravel\YaCommerce\Domain\Order\OrderException;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderItem;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Domain\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Product\Model\InvalidProductException;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\OrderRepository;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipAddressRepository;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\CartItemRepository;

/**
 * CampaignService
 *
 * Uses Eloquent model. It impletents a simplified Builder pattern
 */
class OrderService extends AbstractCrudService implements CrudInterface
{
    /**
     * Tackle the problem that annonymous function can not modify parent scope array for create function
     */
    protected $order_items = [];

    protected $pay_amount = 0;

    /**
     * The anonymous function to Stash away the products data
     */
    protected $purifyData;

    public function __construct($order)
    {
        parent::__construct($order);

        // Stash away the `order_items`, calculate `pay_amount` and generate `order_number`, to make data ready to be created as Order
        $this->purifyData = function ($aData) {
            foreach (['order_items'] as $field) {
                if (isset($aData[$field])) {
                    $this->$field = $aData[$field];
                    unset($aData[$field]);
                }
            }

            $aData['pay_amount'] = $this->pay_amount;
            if (!isset($aData['order_number'])) {
                $aData['order_number'] = $this->ormModel->generateOrderNumber($aData['order_number_prefix']);
            }
            return $aData;
        };
    }

    /**
     * create order and its related orderItems
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void
    {
        $this->ormModel->createNew($data, $this->purifyData, function ($order, $data) {
            // Add order_items
            foreach ($this->order_items as $oItem) {
                $order->addItem($oItem);
            }
            $order->setDescription($this->order_items);
        });
    }


    /**
     * create order and its related orderItems
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        if (isset($data['order_items'])) $this->totalizePayAmount($data);
        $this->ormModel->updateInstance($data, $this->purifyData);
        if (count($this->order_items) > 0) {
            $orderItem = new OrderItem();
            foreach ($this->order_items as $item) {
                $orderItem->updateInstance($item);
            }
        }
    }

    /**
     * Calculate order total
     *
     * @param array $data
     * @return void
     */
    protected function totalizePayAmount($data)
    {
        if (isset($data['pay_amount'])) {
            $this->pay_amount = $data['pay_amount'];
        } else {
            $total = 0;
            foreach ($data['order_items'] as $item) {
                $total += $item['pay_amount'];
            }
            $this->pay_amount = $total;
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function makeOrder(array $data)
    {
        $this->checkQualification($data);
        $this->totalizePayAmount($data);
        if (isset($data['type']) && $data['type'] == 'prepay') {
            $this->makePrepaidOrder($data);
        } else {
            $this->makeShopOrder($data);
        }
    }

    /**
     * @param array $data
     * @return bool
     *  @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    protected function checkQualification(array $data)
    {
        if (isset($data['campaign_id'])) {
            $campaign = Campaign::find($data['campaign_id']);
        }
    }


    /**
     * make prepaid order
     */
    public function makePrepaidOrder(array $data): void
    {
        if (!isset($data['user_id'])) {
            throw new IllegalArgumentException(trans("YaCommerce::message.no-id"), 1565588142);
        }
        $user = resolve(PrepaidUserInterface::class, ['id' => $data['user_id']]);
        $total = self::totalizePayAmount($data) / 100;
        if ($total > $user->getLeftCredit()) {
            throw new IllegalArgumentException(trans("YaCommerce::message.no-enough-credit"), 1565581699);
        }

        $this->create($data);
        $user->deductCredit($total);
    }


    /**
     * make shop order
     */
    public static function makeShopOrder(array $data): array
    {
        $openid = (AppUser::find($data['app_user_id']))->wxopenid;

        $id = OrderService::saveOrder($data, 'SP');
        self::deleteCartItems($data['items']);
        $info = OrderService::getInfoForWxPayUnifiedOrder($id, $openid);

        $prepayId = WxPay::makeUnifiedOrder($info);
        $payload = WxPay::assemblePayload($prepayId);

        return $payload;
    }

    /**
     * 获取用户在指定应用或商店的所有订单
     */
    public static function findAllForUser(int $userId, string $ptype, int $pid): array
    {
        return OrderRepository::findAllForUser($userId, $ptype, $pid);
    }

    /**
     * Save the order data to database
     * return the orderId
     */
    public static function saveOrder(array $data, string $orderNoPrefix): int
    {
        // 处理售后地址
        if (isset($data['shipaddress'])) {
            if (is_array($data['shipaddress'])) {
                $sAddress = ModelFactory::make(ShipAddress::class, array_merge($data['shipaddress'], ['user_id' => $data['user_id']]));
                $data['shipaddress_id'] = ShipAddressRepository::saveGetId($sAddress);
            } else {
                $data['shipaddress_id'] = $data['shipaddress'];
            }
        }

        $deducts = [];
        // 检查库存
        for ($i = 0; $i < count($data['items']); $i++) {
            if (isset($data['items'][$i]['product_id'])) {
                $prodService = "\\Orq\\Laravel\\YaCommerce\\Product\\Service\\" . config('yac.product_service.' . $data['ptype']);
                try {
                    $prodService::decInventory($data['items'][$i]['product_id'], $data['items'][$i]['amount']);
                    array_push($deducts, [$data['items'][$i]['product_id'], $data['items'][$i]['amount']]);
                } catch (InventoryException | InvalidProductException $e) {
                    // 商品库存不够，回滚
                    $prodService::incInventoryForProducts($deducts);
                    $msg = $data['items'][$i]['product_id'] . ':' . $data['items'][$i]['title'] . '库存不够！';
                    Log::error($msg);
                    throw new OrderException($msg, 1565750457);
                }
            }
        }

        // 保存订单和订单项
        $order = ModelFactory::make(Order::class, $data, [$orderNoPrefix]);
        if (!isset($data['pay_amount'])) {
            $order->setPayAmount(self::totalizePayAmount($data));
        }

        $id = OrderRepository::saveGetId($order);
        OrderRepository::saveOrderItems($id, $data['items']);

        event(new SavedOrder($data));
        return $id;
    }

    /**
     * Assemble information to make WeChat UnifiedOrder
     */
    public static function getInfoForWxPayUnifiedOrder(int $id, string $openid): array
    {
        $order = OrderRepository::findById($id, true);

        return ['body' => $order->getDescription(), 'out_trade_no' => $order->getOrderNumber(), 'total_fee' => $order->getPayAmount(), 'openid' => $openid];
    }


    /**
     * 删除购物车记录
     */
    protected static function deleteCartItems(array $items)
    {
        $cItemIds = [];
        foreach ($items as $item) {
            if (isset($item['cartitem_id'])) {
                array_push($cItemIds, $item['cartitem_id']);
            }
        }

        CartItemRepository::deleteItems($cItemIds);
    }

    /**
     * 创建订单
     */
    public static function makeSeckillOrder(array $data): array
    {
        $openid = (AppUser::find($data['app_user_id']))->wxopenid;

        $id = OrderService::saveOrder($data, 'SK');
        $info = OrderService::getInfoForWxPayUnifiedOrder($id, $openid);

        $prepayId = WxPay::makeUnifiedOrder($info);
        $payload = WxPay::assemblePayload($prepayId);

        return $payload;
    }

    /**
     * 再次支付
     */
    public static function repay(int $orderId, User $wqbUser)
    {
        $openid = (AppUser::find($wqbUser->getUserId()))->wxopenid;
        $info = OrderService::getInfoForWxPayUnifiedOrder($orderId, $openid);

        $prepayId = WxPay::makeUnifiedOrder($info);
        $payload = WxPay::assemblePayload($prepayId);

        return $payload;
    }
}
