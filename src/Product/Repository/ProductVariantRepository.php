<?php

namespace Orq\Laravel\YaCommerce\Product\Repository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orq\DddBase\Repository\AbstractRepository;
use Orq\Laravel\YaCommerce\Product\Model\ProductVariant;

class ProductVariantRepository extends AbstractRepository
{
    protected static $table = 'yac_product_variants';
    protected static $class = ProductVariant::class;


    public static function incInventoryForProducts(array $data): void
    {
        foreach ($data as $d) {
            DB::table(self::$table)->where('id', $d[0])->increment('inventory', $d[1]);
        }
    }
}
