<?php

namespace Orq\Laravel\YaCommerce\Order\Service;

use App\User as AppUser;
use Orq\DddBase\ModelFactory;
use Orq\DddBase\DomainException;
use App\MicroGroup\Domain\Model\User;
use Orq\Laravel\YaCommerce\Payment\WxPay;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Order\Model\Order;
use Orq\Laravel\YaCommerce\Order\OrderException;
use Orq\Laravel\YaCommerce\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Order\Repository\CartItemRepository;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;
use Orq\Laravel\YaCommerce\Order\Repository\OrderRepository;
use Orq\Laravel\YaCommerce\Product\Model\InvalidProductException;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipAddressRepository;

class OrderService
{

    /**
     * Save the order data to database
     * return the orderId
     */
    public static function saveOrder(array $data, string $orderNoPrefix): int
    {
         // 处理售后地址
         if (isset($data['shipaddress'])) {
            if (is_array($data['shipaddress'])) {
                $sAddress = ModelFactory::make(ShipAddress::class, array_merge($data['shipaddress'], ['user_id'=>$data['user_id']]));
                $data['shipaddress_id'] = ShipAddressRepository::saveGetId($sAddress);
            } else {
                $data['shipaddress_id'] = $data['shipaddress'];
            }
        }

        $deducts = [];
        // 检查库存
        for ($i=0; $i<count($data['items']); $i++) {
            if (isset($data['items'][$i]['product_id'])) {
                $prodService = "\\Orq\\Laravel\\YaCommerce\\Product\\Service\\".config('yac.product_service.'.$data['ptype']);
                try {
                    $prodService::decInventory($data['items'][$i]['product_id'], $data['items'][$i]['amount']);
                    array_push($deducts, [$data['items'][$i]['product_id'], $data['items'][$i]['amount']]);
                } catch (InventoryException | InvalidProductException $e) {
                    // 商品库存不够，回滚
                    $prodService::incInventoryForProducts($deducts);
                    $msg = $data['items'][$i]['product_id'].':'.$data['items'][$i]['title'].'库存不够！';
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
    public static function findAllForUser(int $userId, string $ptype, int $pid):array
    {
        return OrderRepository::findAllForUser($userId, $ptype, $pid);
    }

    /**
     * 创建订单、删除购物车
     */
    public static function makeShopOrder(array $data):array
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
    protected static function deleteCartItems(array $items) {
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
    public static function makeSeckillOrder(array $data):array
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
