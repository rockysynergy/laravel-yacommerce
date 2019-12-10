<?php

namespace Orq\Laravel\YaCommerce\AppServices\Admin;

use Exception;
use Orq\DddBase\ModelFactory;
use Orq\DddBase\DomainException;
use Orq\DddBase\IllegalArgumentException;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Order\Model\Order;
use Orq\Laravel\YaCommerce\Events\ChangeShipnumber;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipTracking;
use Orq\Laravel\YaCommerce\Shop\Repository\ShopRepository;
use Orq\Laravel\YaCommerce\Order\Repository\OrderRepository;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipAddressRepository;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipTrackingRepository;

class YacOrderService
{

    public static function getOrdersForShop(int $shopId, array $filter): array
    {
        $shopType = ShopRepository::getType($shopId)->type;
        $orderRepository = resolve(OrderRepository::class);
        $result = $orderRepository->findAllFor($shopType, $shopId, $filter);
        return $result;
    }

    public static function saveNew(array $data): void
    {
        $order = ModelFactory::make(Order::class, $data);
        OrderRepository::save($order);
    }

    public static function getById(int $id): ?array
    {
        $d = OrderRepository::findByIdWithTracking($id);
        return $d;
    }

    public static function updateItem(array $data): void
    {
        $data['pay_amount'] *= 100;
        if (isset($data['shipnumber'])) {
            try {
                $shipTracking = ShipTrackingRepository::findOne([['order_id', '=', $data['id']]], true);
                if ($shipTracking) {
                    $shipTracking->setShipnumber($data['shipnumber']);
                    $shipTracking->setCarrier($data['carrier']);
                    ShipTrackingRepository::update($shipTracking);

                    // trigger the ChangeShipnumber event
                    if ($shipTracking->getShipnumber() != $data['shipnumber']) {
                        $shipTrackingId = $shipTracking->getId();
                        event(new ChangeShipnumber([$shipTrackingId, $data['shipnumber'], ShipAddressRepository::getMobile((int) $data['shipaddress_id'])]));
                    }
                 } else {
                    $shipTracking = ModelFactory::make(ShipTracking::class, ['order_id' => $data['id'], 'shipnumber' => $data['shipnumber'], 'carrier' => $data['carrier'], 'shipaddress_id' => $data['shipaddress_id']]);
                    $shipTrackingId = ShipTrackingRepository::saveGetId($shipTracking);
                    event(new ChangeShipnumber([$shipTrackingId, $data['shipnumber'], ShipAddressRepository::getMobile((int) $data['shipaddress_id'])]));
                }
            } catch (DomainException | IllegalArgumentException $e) {
                throw $e;
            }
        }
        $order = OrderRepository::findById($data['id'], true);
        $order = ModelFactory::update($order, $data);
        OrderRepository::update($order);
    }
}
