<?php
namespace Orq\Laravel\YaCommerce\Domain\Shop\Service;

use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\Shop\Model\Shop;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Product\Model\Category;
use Orq\Laravel\YaCommerce\Domain\Shop\Repository\ShopRepository;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;
use Orq\Laravel\YaCommerce\Product\Repository\CategoryRepository;
use Orq\Laravel\YaCommerce\Product\Service\CategoryService;

class ShopService
{
    /**
     * @return array of Product instances
     */
    public static function getAllProductsForShop(int $shopId, string $shopType, bool $showAll=true, array $filter = []):array
    {
        $prodService = "\\Orq\\Laravel\\YaCommerce\\Product\\Service\\".config('yac.product_service.'.$shopType);
        return $prodService::getAllForShop($shopId, $showAll, $filter);
    }

    public static function createShop(string $type, string $name):int
    {
        $shop = ModelFactory::make(Shop::class, ['type'=>$type, 'name'=>$name]);
        $sId = ShopRepository::saveGetId($shop);
        $category = ModelFactory::make(Category::class, ['title'=>'默认分类', 'shop_id'=>$sId]);
        CategoryRepository::save($category);

        return $sId;
    }

    public static function deleteShop(int $shopId):void
    {
        CategoryService::deleteAllForShop($shopId);
        ShopRepository::removeById($shopId);
    }
}
