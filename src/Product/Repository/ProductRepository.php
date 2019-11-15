<?php

namespace Orq\Laravel\YaCommerce\Product\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Product\Model\Product;
use Orq\DddBase\Repository\AbstractRepository;

class ProductRepository extends AbstractRepository
{
    protected static $table = 'yac_products';
    protected static $class = Product::class;

    public static function getAllByCategoryIds(array $catIds): Collection
    {
        return DB::table(self::$table)->whereIn('category_id', $catIds)->get();
    }

    public static function getAllByShopId(int $shopId, bool $showAll = true): Collection
    {
        if ($showAll) {
            return DB::table('yac_products')
                ->join('yac_categories', function ($join) use ($shopId) {
                    $join->on('yac_products.category_id', '=', 'yac_categories.id')
                        ->where('yac_categories.shop_id', '=', $shopId);
                })
                ->select('yac_products.*', 'yac_categories.title as category')
                ->get();
        } else {
            return DB::table('yac_products')
                ->join('yac_categories', function ($join) use ($shopId) {
                    $join->on('yac_products.category_id', '=', 'yac_categories.id')
                        ->where('yac_categories.shop_id', '=', $shopId);
                })
                ->where('yac_products.status', 1)
                ->where('inventory', '>', 0)
                ->select('yac_products.*', 'yac_categories.title as category')
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
