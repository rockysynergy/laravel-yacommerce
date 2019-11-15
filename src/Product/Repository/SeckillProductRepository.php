<?php

namespace Orq\Laravel\YaCommerce\Product\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\Product\Model\SeckillProduct;

class SeckillProductRepository extends AbstractRepository
{
    protected static $table = 'yac_seckillproducts';
    protected static $class = SeckillProduct::class;

    public static function getAllByCategoryIds(array $catIds): Collection
    {
        return DB::table(self::$table)->whereIn('category_id', $catIds)->get();
    }

    public static function getAllByShopId(int $shopId, bool $showAll = true): Collection
    {
        if ($showAll) {
            $re = DB::table('yac_seckillproducts')
                ->join('yac_categories', function ($join) use ($shopId) {
                    $join->on('yac_seckillproducts.category_id', '=', 'yac_categories.id')
                        ->where('yac_categories.shop_id', '=', $shopId);
                })
                ->select('yac_seckillproducts.*', 'yac_categories.title as category')
                ->get();
            return $re;
        } else {
            return DB::table('yac_seckillproducts')
                ->join('yac_categories', function ($join) use ($shopId) {
                    $join->on('yac_seckillproducts.category_id', '=', 'yac_categories.id')
                        ->where('yac_categories.shop_id', '=', $shopId);
                })
                ->where('yac_seckillproducts.status', 1)
                // ->where('inventory', '>', 0)
                ->select('yac_seckillproducts.*', 'yac_categories.title as category')
                ->get();
        }
    }

    public static function incInventoryForProducts(array $data): void
    {
        foreach ($data as $d) {
            DB::table(self::$table)->where('id', $d[0])->increment('inventory', $d[1]);
        }
    }
}
