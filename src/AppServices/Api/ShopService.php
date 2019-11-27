<?php

namespace Orq\Laravel\YaCommerce\AppServices\Api;

use Orq\DddBase\IllegalArgumentException;
use App\Http\Service\WxService;
use Orq\Laravel\YaCommerce\Order\Service\OrderService;
use Orq\Laravel\YaCommerce\Shop\Repository\ShopRepository;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;
use Orq\Laravel\YaCommerce\Product\Repository\ProductVariantRepository;
use Orq\Laravel\YaCommerce\Shop\Service\ShopService as YacShopService;
use Orq\Laravel\YaCommerce\Product\Repository\SeckillProductRepository;

class ShopService
{
    public static function getAllProductForShop(int $shopId, bool $showAll = false): array
    {
        $shopType = ShopRepository::getType($shopId)->type;
        $rs = YacShopService::getAllProductsForShop($shopId, $shopType, $showAll);
        $a = [];
        if ($shopType == 'seckill') {
            foreach ($rs as $r) {
                if (!$showAll && $r->isFinished()) continue;

                $inFields = ['id', 'title','price', 'sk_price', 'status_text', 'progress',  'start_time', 'end_time',
            'total', 'sold', 'cover_pic', 'status', 'inventory'];
                array_push($a, $r->getData($inFields));
            }
        } else {
            foreach ($rs as $r) {
                $product = $r->getData(['id', 'title', 'price', 'status', 'cover_pic', 'inventory']);
                $product['variants'] = ProductVariantRepository::find([['product_id', '=', $r->getId()]], true)->toArray();
                array_push($a, $product);
            }
        }

        return $a;
    }

    public static function getProductInfo(int $id, int $shopId): array
    {
        $shopType = ShopRepository::getType($shopId)->type;
        if ($shopType == 'seckill') {
            $skProd = SeckillProductRepository::findById($id, true);
            $inFields = ['id', 'title','price', 'sk_price', 'status_text', 'progress',  'start_time', 'end_time',
            'total', 'sold', 'cover_pic', 'inventory', 'description', 'pictures'];
            $p = $skProd->getData($inFields);
        } else {
            $p = ProductRepository::findById($id);
            $p['variants'] = ProductVariantRepository::find([['product_id', '=', $id]], true)->toArray();
        }

        $p['detail'] = WxService::parseHtml($p['description'], config('app.url'));
        $pics = explode(',', $p['pictures']);
        foreach ($pics as $k=>$pic) {
            $pics[$k] = config('app.url').$pic;
        }
        $p['pictures'] = $pics;
        return $p;
    }

    public static function makeOrder(array $data)
    {
        if (!$data['shop_type']) {
            throw new IllegalArgumentException('请提供店铺类型！', 1569815891);
        }

        $data['ptype'] = $data['shop_type'];
        if (strtolower($data['shop_type']) == 'bp_shop') {
            $bpService = \resolve('Orq\Laravel\YaCommerce\Order\PrepaidUserInterface', ['userId' => (int)$data['user_id']]);
            OrderService::makePrepaidOrder($data, $bpService);
        }

        if (strtolower($data['shop_type']) == 'shop') {
            return OrderService::makeShopOrder($data);
        }

        if (strtolower($data['shop_type']) == 'seckill') {
            return OrderService::makeSeckillOrder($data);
        }
    }
}
