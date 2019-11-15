<?php
namespace Orq\Laravel\YaCommerce\Shop\Repository;

use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Shop\Model\Shop;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\Product\Repository\CategoryRepository;

class ShopRepository extends AbstractRepository
{
    protected static $table = 'yac_shops';
    protected static $class = Shop::class;

    public static function getType(int $shopId):object
    {
        return DB::table(self::$table)->select('type')->where('id', $shopId)->first();
    }

    public static function deleteShop(int $shopId):void
    {
        CategoryRepository::delete([['shop_id', '=', $shopId]]);
        self::delete([['id', '=', $shopId]]);
    }
}
