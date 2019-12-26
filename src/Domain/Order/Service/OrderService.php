<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Payment\WxPay;
use Orq\Laravel\YaCommerce\Events\SavedOrder;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\UserInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfo;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderItem;
use Orq\Laravel\YaCommerce\Domain\UserRepositoryInterface;
use Orq\Laravel\YaCommerce\Domain\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\OrderRepository;
use Orq\Laravel\YaCommerce\Domain\Order\Model\CartItemServiceInterface;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignRepositoryInterface;
use Orq\Laravel\YaCommerce\Domain\Product\Service\ProductService;

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
    protected $shiaddress = [];

    protected $pay_amount = 0;
    protected $campaign = null;
    protected $order = null;

    /**
     * The anonymous function to Stash away the products data
     */
    protected $purifyData;

    public function __construct($order = null)
    {
        if (is_null($order)) $order = new Order();
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

            $this->order = $order;
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
     * @param object $user implements PrepaidUserInerface or UserInteface
     * @return array
     */
    public function makeOrder(array $data, $user):array
    {
        $orderInfo = new OrderInfo($data);
        $payload = [];

        // if campaign presents, check the qualification and calculate the pay_amount
        //@todo How about multiple campains?
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
            if ($data['type'] == 'prepay') $this->makePrepaidOrder($data, $user);
        } else {
            $payload = $this->makeShopOrder($data, $user);
        }

        $this->deleteCartItems();
        return $payload;
    }

    /**
     * make prepaid order
     *
     * @param array $data
     * @param PrepaiedUserInterface $user
     *
     * @return void
     */
    protected function makePrepaidOrder(array $data, PrepaidUserInterface $user): void
    {
        $total = $data['pay_amount'] / 100;
        if ($total > $user->getLeftCredit()) {
            throw new IllegalArgumentException(trans("YaCommerce::message.no-enough-credit"), 1565581699);
        }

        try {
            DB::beginTransaction();
            $productService = resolve(ProductService::class);
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
     *
     * @param array $data
     * @param PrepaiedUserInterface $user
     *
     * @return array the payload for FE code to start the paying
     */
    protected function makeShopOrder(array $data, UserInterface $user): array
    {
        try {
            DB::beginTransaction();
            $productService = resolve(ProductService::class);
            foreach ($data['order_items'] as $orderItem) {
                $productService->decInventory($orderItem['product_id'], $orderItem['amount']);
            }
            $this->create($data);
            DB::commit();
        } catch (IllegalArgumentException $e) {
            throw $e;
            DB::rollBack();
        }

        $payload = !is_null($this->order) ? $this->makePayload($this->order, $user) : [];
        return $payload;
    }


    /**
     * repay the order
     *
     * @param array $orderId
     * @param PrepaiedUserInterface $user
     *
     * @return array the payload for FE code to start the paying
     */
    public function repay($orderId, $user)
    {
        $info = $this->makePayload($orderId, $user);

        $prepayId = WxPay::makeUnifiedOrder($info);
        $payload = WxPay::assemblePayload($prepayId);

        return $payload;
    }

    /**
     * @param mix $order_id | $order
     * @param PrepaiedUserInterface $user
     *
     * @return array the payload for FE code to start the paying
     */
    protected function makePayload($order, $user):array
    {
        if (!is_object($order)) {
            $order = Order::find($order);
        }
        $info = ['body' => $order->description, 'out_trade_no' => $order->order_number, 'total_fee' => $order->pay_amount, 'openid' => $user->getWxOpenId()];

        $prepayId = WxPay::makeUnifiedOrder($info);
        return WxPay::assemblePayload($prepayId);
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

        $cartService = resolve(CartItemService::class);
        $cartService->deleteItems($cItemIds);
    }

    /**
     * Find all orders for the user
     * @param array $filter
     */
    public function findAllForUser(array $filter): Collection
    {
        return $this->ormModel->findAllOrders($filter);
    }

}
