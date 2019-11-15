<?php
namespace Orq\Laravel\YaCommerce\Shipment\Repository;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\DB;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipTracking;

class ShipTrackingRepository extends AbstractRepository
{
    protected static $table = 'yac_shiptrackings';
    protected static $class = ShipTracking::class;

    public static function updateStatus(int $shipTrackingId, int $status)
    {
        DB::table(self::$table)->where('id', $shipTrackingId)->update(['tracking_status' => $status]);
    }

    public static function findOneByShipnumber(string $shipnumber): ?ShipTracking
    {
        $record = DB::table(self::$table)->where('shipnumber', '=', trim($shipnumber))->first();
        if ($record) {
            return ModelFactory::make(self::$class, json_decode(json_encode($record), true));
        }

        return null;
    }

    public static function getTrackingFor(int $shipTrackingId):array
    {
        $data = DB::table(self::$table)->where('id', $shipTrackingId)->select('tracking')->first();
        if ($data) {
            return json_decode($data->tracking, true);
        } else {
            return [];
        }
    }
}
