<?php

namespace Orq\Laravel\YaCommerce\AppServices\Admin;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Product\Model\Product;
use Orq\Laravel\YaCommerce\AppServices\Api\ShopService;
use Orq\Laravel\YaCommerce\Product\Model\SeckillProduct;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;
use Orq\Laravel\YaCommerce\Product\Repository\SeckillProductRepository;

class YacProductService
{

    public static function getProductsForShop(int $shopId, bool $showAll = false):array
    {
        $result = ShopService::getAllProductForShop($shopId, $showAll);
        return $result;
    }

    public static function saveNew(array $data):void
    {
        if ($data['shop_type'] != 'bp_shop') $data['price'] *= 100;
        if (isset($data['sk_price'])) $data['sk_price'] *= 100;

        if ($data['shop_type'] == 'seckill') {
            $data['total'] = $data['inventory'];
            $prod = ModelFactory::make(SeckillProduct::class, $data);
            SeckillProductRepository::save($prod);
        } else {
            $prod = ModelFactory::make(Product::class, $data);
            ProductRepository::save($prod);
        }
    }

    public static function getById(int $id, string $shopType):object
    {
        if ($shopType == 'seckill') {
            $product = SeckillProductRepository::findById($id, true);
            return $product;
        } else {
            $product = ProductRepository::findById($id, true);
            return $product;
        }
    }

    public static function updateItem(array $data):void
    {
        if ($data['shop_type'] != 'bp_shop') $data['price'] *= 100;
        if ($data['shop_type'] != 'bp_shop') $data['show_price'] *= 100;
        if (isset($data['sk_price'])) $data['sk_price'] *= 100;

        if ($data['shop_type'] == 'seckill') {
            $prod = ModelFactory::make(SeckillProduct::class, $data);
            SeckillProductRepository::update($prod);
        } else {
            $prod = ModelFactory::make(Product::class, $data);
            ProductRepository::update($prod);
        }
    }
}
