<?php
namespace Orq\Laravel\YaCommerce\Shipment\Repository;

use Illuminate\Support\Collection;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;
use Illuminate\Support\Facades\DB;

class ShipAddressRepository extends AbstractRepository
{
    protected static $table = 'yac_shipaddresses';
    protected static $class = ShipAddress::class;

    public static function findForUser(int $userId):Collection
    {
        return self::find([['user_id', '=', $userId]], true);
    }

    public static function getMobile(int $id): string
    {
        $data = DB::table(self::$table)->select('mobile')->where('id', $id)->first();
        return $data->mobile;
    }
}
