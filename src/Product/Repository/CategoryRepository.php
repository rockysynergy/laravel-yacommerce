<?php
namespace Orq\Laravel\YaCommerce\Product\Repository;

use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Product\Model\Category;
use Orq\DddBase\Repository\AbstractRepository;

class CategoryRepository extends AbstractRepository
{
    protected static $table = 'yac_categories';
    protected static $class = Category::class;

    public static function getAllIdsForShop(int $shopId):array
    {
        return DB::table(self::$table)->where('shop_id', $shopId)->pluck('id')->toArray();
    }

    public static function getAllForShop(int $shopId):array
    {
        $re = DB::table(self::$table)->where('shop_id', $shopId)->get()->toArray();
        $arr = [];
        foreach ($re as $r) {
            array_push($arr, \json_decode(json_encode($r),true));
        }
        return $arr;
    }
}
