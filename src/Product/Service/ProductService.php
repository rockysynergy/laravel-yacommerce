<?php
namespace Orq\Laravel\YaCommerce\Product\Service;

use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\Product;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;

class ProductService
{

    public static function getAllForShop(int $shopId, bool $showAll)
    {
        $items = ProductRepository::getAllByShopId($shopId, $showAll);
        $arr = [];
        foreach ($items as $item) {
            $d = json_decode(json_encode($item), true);
            array_push($arr, ModelFactory::make(Product::class, $d));
        }
        return $arr;
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
