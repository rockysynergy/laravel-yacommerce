<?php

namespace Orq\Laravel\YaCommerce\AppServices\Admin;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Product\Model\Category;
use Orq\Laravel\YaCommerce\Product\Service\CategoryService;
use Orq\Laravel\YaCommerce\Product\Repository\CategoryRepository;

class YacCategoryService
{

    public static function getCategoriesForShop(int $shopId):array
    {
        $result = CategoryService::getAllForShop($shopId);
        return $result;
    }

    public static function getAllSiblingCategories(int $categoryId):array
    {
        $shopId = CategoryRepository::findById($categoryId, true)->getShopId();
        return self::getCategoriesForShop($shopId);
    }

    public static function saveNew(array $data):void
    {
        $cat = ModelFactory::make(Category::class, $data);
        CategoryRepository::save($cat);
    }

    public static function getById(int $id):object
    {
        return CategoryRepository::findById($id, true);
    }

    public static function updateItem(array $data):void
    {
        $cat = ModelFactory::make(Category::class, $data);
        CategoryRepository::update($cat);
    }
}
