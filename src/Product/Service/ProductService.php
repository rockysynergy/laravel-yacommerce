<?php
namespace Orq\Laravel\YaCommerce\Product\Service;

use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\Product;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;

class ProductService
{

    public static function getAllForShop(int $shopId, bool $showAll, array $filter = [])
    {
        $prodRep = resolve(ProductRepository::class);
        $items = $prodRep->getAllByShopId($shopId, $showAll, $filter);
        $arr = [];
        foreach ($items['data'] as $item) {
            $d = json_decode(json_encode($item), true);
            array_push($arr, ModelFactory::make(Product::class, $d));
        }
        return ['count' => $items['count'], 'data' => $arr];
    }

    public static function getById(int $id):array
    {
        return ProductRepository::findById($id);
    }

    public static function decInventory(int $prodId, int $num):void
    {
        $prod = ProductRepository::findById($prodId, true);
        $prod->decInventory($num);
        ProductRepository::update($prod);
    }

    public static function incInventoryForProducts(array $data):void
    {
        ProductRepository::incInventoryForProducts($data);
    }
}
