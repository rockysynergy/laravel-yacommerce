<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Service;

use App\User as AppUser;
use Orq\DddBase\ModelFactory;
use Orq\DddBase\DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\MicroGroup\Domain\Model\User;
use Orq\Laravel\YaCommerce\Payment\WxPay;
use Orq\Laravel\YaCommerce\Events\SavedOrder;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\UserInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;
use Orq\Laravel\YaCommerce\Domain\Order\OrderException;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfo;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderItem;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Domain\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Product\Model\InvalidProductException;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\OrderRepository;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipAddressRepository;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\CartItemRepository;
use Orq\Laravel\YaCommerce\Domain\Product\Service\ProductServiceInterface;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignRepositoryInterface;

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

    protected $campaign = null;

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
            $productIds = [];
            foreach ($this->order_items as $oItem) {
                $order->addItem($oItem);
                array_push($productIds, $oItem['product_id']);
            }
            $order->save();

            // Add participate record
            if (!is_null($this->campaign)) $this->campaign->addParticipate([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'products' => implode(',', $productIds),
            ]);

            // emit SavedOrder event
            event(new SavedOrder($data));
        });
    }


    /**
     * create order and its related orderItems
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        $this->ormModel->updateInstance($data, $this->purifyData);

        if (count($this->order_items) > 0) {
            $orderItem = new OrderItem();
            foreach ($this->order_items as $item) {
                $orderItem->updateInstance($item);
            }
        }
    }

    /**
     * @param array $data
     * @param Orq\Laravel\YaCommerce\Domain\UserInterface $user
     * @return void
     */
    public function makeOrder(array $data, UserInterface $user)
    {
        $orderInfo = new OrderInfo($data, $user);

        // if campaign presents, check the qualification and calculate the pay_amount
        if (isset($data['campaign_id'])) {
            $campaignRepository = resolve(CampaignRepositoryInterface::class);
            $campaign = $campaignRepository->findById($data['campaign_id']);
            if (!$campaign->isQualified($orderInfo)) throw new IllegalArgumentException(trans("YaCommerce::message.campaign-not-qualified"), 1577245156);
            $orderInfo->updatePayTotal($campaign->calculatePrice($orderInfo));
            $this->campaign = $campaign;
        }

        $data['order_number'] = $this->ormModel->generateOrderNumber($data['order_number_prefix']);
        $data['pay_amount'] = $orderInfo->getPayTotal();
        $data['description'] = $orderInfo->getDescription();
        if (isset($data['type'])) {
            if ($data['type'] == 'prepay') $this->makePrepaidOrder($data);
        } else {
            $this->makeShopOrder($data);
        }

        $this->deleteCartItems();
    }

    /**
     * make prepaid order
     */
    public function makePrepaidOrder(array $data): void
    {
        $user = resolve(PrepaidUserInterface::class, ['id' => $data['user_id']]);
        $total = $data['pay_amount'] / 100;
        if ($total > $user->getLeftCredit()) {
            throw new IllegalArgumentException(trans("YaCommerce::message.no-enough-credit"), 1565581699);
        }

        try {
            DB::beginTransaction();
            $productService = resolve(ProductServiceInterface::class);
            foreach ($data['order_items'] as $orderItem) {
                $productService->decInventory($orderItem['product_id'], $orderItem['amount']);
            }
            $this->create($data);
            $user->deductCredit($total);
            DB::commit();
        } catch (IllegalArgumentException $e) {
            throw $e;
            DB::rollBack();
        }
    }


    /**
     * make shop order
     */
    public function makeShopOrder(array $data): array
    {
        try {
            DB::beginTransaction();
            $productService = resolve(ProductServiceInterface::class);
            foreach ($data['order_items'] as $orderItem) {
                $productService->decInventory($orderItem['product_id'], $orderItem['amount']);
            }
            $this->create($data);
            DB::commit();
        } catch (IllegalArgumentException $e) {
            throw $e;
            DB::rollBack();
        }

        $openid = (AppUser::find($data['app_user_id']))->wxopenid;
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
     * Assemble information to make WeChat UnifiedOrder
     */
    public static function getInfoForWxPayUnifiedOrder(int $id, string $openid): array
    {
        $order = OrderRepository::findById($id, true);

        return ['body' => $order->getDescription(), 'out_trade_no' => $order->getOrderNumber(), 'total_fee' => $order->getPayAmount(), 'openid' => $openid];
    }


    /**
     * Delete the CartItem
     */
    protected function deleteCartItems()
    {
        $cItemIds = [];
        foreach ($this->order_items as $item) {
            if (isset($item['cartitem_id'])) {
                array_push($cItemIds, $item['cartitem_id']);
            }
        }

        $cartService = resolve(CartItemServiceInterface::class);
        $cartService->deleteItems($cItemIds);
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
