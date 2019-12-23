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
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;
use Orq\Laravel\YaCommerce\Domain\Order\OrderException;
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

    /**
     * The anonymous function to Stash away the products data
     */
    protected $purifyData;

    public function __construct($order)
    {
        parent::__construct($order);

        $this->purifyData = function ($aData) {
            $payTotal = 0;
            foreach (['order_items'] as $field) {
                if (isset($aData[$field])) {
                    array_walk($aData['$field'], function($item, $k, &$total) {
                        $total += $item['pay_amount'];
                    }, $payTotal);
                    $this->$field = $aData[$field];
                    unset($aData[$field]);
                }
            }

            $aData['pay_amount'] = $payTotal;
            $aData['order_number'] = $this->ormModel->generateOrderNumber($aData['order_number_prefix']);
            return $aData;
        };
    }

    /**
     * create campaign and its related products
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

    // 类似于积分兑换的订单
    public static function makePrepaidOrder(array $data, PrepaidUserInterface $user): void
    {
        if (!$user instanceof PrepaidUserInterface) {
            throw new \Exception('请提供实现了PrepaidUserInterface的用户实例！', 1565588142);
        }
        $total = self::totalizePayAmount($data) / 100;
        if ($total > $user->getLeftCredit()) {
            throw new DomainException('余额（积分）不足！', 1565581699);
        }

        self::saveOrder($data, 'JF');
        $user->deductCredit($total);
    }

    // 计算订单总额
    protected static function totalizePayAmount($data)
    {
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['pay_amount'];
        }
        return $total;
    }

    /**
     * 获取用户在指定应用或商店的所有订单
     */
    public static function findAllForUser(int $userId, string $ptype, int $pid): array
    {
        return OrderRepository::findAllForUser($userId, $ptype, $pid);
    }

    /**
     * 创建订单、删除购物车
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
