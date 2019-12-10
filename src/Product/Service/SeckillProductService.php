<?php
namespace Orq\Laravel\YaCommerce\Product\Service;

use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\SeckillProduct;
use Orq\Laravel\YaCommerce\Product\Repository\SeckillProductRepository;

class SeckillProductService
{

    public static function getAllForShop(int $shopId, bool $showAll, array $filter = [])
    {
        $prodRep = resolve(SeckillProductRepository::class);
        $items = $prodRep->getAllByShopId($shopId, $showAll, $filter);
        $arr = [];
        foreach ($items['data'] as $item) {
            $d = json_decode(json_encode($item), true);
            array_push($arr, ModelFactory::make(SeckillProduct::class, $d));
        }
        return ['count' => $items['count'], 'data' => $arr];
    }

    public static function getById(int $id):array
    {
        return SeckillProductRepository::findById($id);
    }

    public static function decInventory(int $prodId, int $num):void
    {
        $prod = SeckillProductRepository::findById($prodId, true);
        $prod->decInventory($num);
        SeckillProductRepository::update($prod);

    }

    public static function incInventoryForProducts(array $data):void
    {
        SeckillProductRepository::incInventoryForProducts($data);
    }
}
