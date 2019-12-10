<?php

namespace Orq\Laravel\YaCommerce\Order\Repository;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Order\Model\Order;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\BeListTrait;
use Orq\Laravel\YaCommerce\Order\Model\OrderItem;
use Orq\Laravel\YaCommerce\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipTrackingRepository;

class OrderRepository extends AbstractRepository
{
    use BeListTrait;

    protected static $table = 'yac_orders';
    protected static $class = Order::class;


    /**
     * For front end order listing
     */
    public static function findAllForUser(int $userId, string $ptype, int $pid): array
    {
        $orders = DB::table(self::$table)->where([['user_id', '=', $userId], ['ptype', '=', $ptype], ['pid', '=', $pid]])->orderBy('created_at', 'desc')->get();
        $orders = json_decode(json_encode($orders), true);
        foreach ($orders as $k => $order) {
            $orders[$k]['items'] = self::__getItems($order['id']);
            if (!is_null($order['shiptracking_id'])) {
                $orders[$k]['shiptracking'] = ShipTrackingRepository::getTrackingFor($order['shiptracking_id']);
            }
        }
        return $orders;
    }

    public static function findById(int $id, bool $toObj = false)
    {
        $order = parent::findById($id, $toObj);
        $aItems = self::__getItems($id, $toObj);
        if ($toObj) {
            $order->setItems($aItems);
        } else {
            $order['items'] = $aItems;
        }
        return $order;
    }

    private static function __getItems(int $orderId, bool $toObj = false): array
    {
        $items = DB::table('yac_orderitems')->where([['order_id', '=', $orderId]])->get();
        $aItems = [];
        foreach ($items as $item) {
            $item = json_decode(json_encode($item), true);
            if ($toObj) {
                array_push($aItems, ModelFactory::make(OrderItem::class, $item));
            } else {
                array_push($aItems, $item);
            }
        }
        return $aItems;
    }

    public static function saveOrderItems(int $orderId, array $items): void
    {
        $aItems = [];
        foreach ($items as $item) {
            $item['order_id'] = $orderId;
            $item = ModelFactory::make(OrderItem::class, $item);
            array_push($aItems, $item->getPersistData());
        }
        Db::table('yac_orderitems')->insert($aItems);
    }

    /*
     * 跟订单号查找
     *
     * @return void or the Order instance
     */
    public static function findByOrderNumber(string $orderNumber)
    {
        if (mb_strlen($orderNumber) < 1) {
            throw new IllegalArgumentException('请提供合法的订单号！', 1564968852);
        }

        $re = self::find([['order_number', '=', $orderNumber]])->get(0);
        if ($re) {
            return ModelFactory::make(self::$class, $re);
        }
    }

    /**
     * For admin order listing
     */
    public static function findAllFor(string $ptype, int $pid, array $filter)
    {
        // return self::find([['ptype', '=', $ptype], ['pid', '=', $pid]])->toArray();
        $aQuery = DB::table(self::$table);
        if (isset($filter['filterOrderNumber'])) $aQuery = $aQuery->where('order_number', '=', $filter['filterOrderNumber']);
        if (isset($filter['filterCreatedAt'])) $aQuery = $aQuery->where('order_number', '=', $filter['filterCreatedAt']);

        $query = DB::table('yac_shipaddresses as B');
        if (isset($filter['filterName'])) $query = $query->where('name', '=', $filter['filterName']);
        if (isset($filter['filterMobile'])) $query = $query->where('mobile', '=', $filter['filterMobile']);
        $query->leftJoinSub($query, 'A', function ($join) use ($ptype, $pid) {
            $join->on('A.shipaddress_id', '=', 'B.id')
            ->select('A.*', 'B.name', 'B.mobile', 'B.address')
            ->where('A.ptype', '=', $ptype)
            ->where('A.pid', '=', $pid);
        });

        return self::paginate($query, $filter);
    }

    public static function findByIdWithTracking(int $id)
    {
        $d = (DB::table(self::$table . ' as A')
            ->leftJoin('yac_shiptrackings as B', 'B.order_id', '=', 'A.id')
            ->select('A.*', 'B.shipnumber', 'B.carrier')
            ->where('A.id', '=', $id)
            ->get()
            ->toArray())[0];
        return json_decode(json_encode($d), true);
    }
}
