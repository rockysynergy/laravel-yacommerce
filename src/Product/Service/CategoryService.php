<?php
namespace Orq\Laravel\YaCommerce\Product\Service;

use Orq\Laravel\YaCommerce\Product\Repository\CategoryRepository;

class CategoryService
{

    public static function getAllForShop(int $shopId):array
    {
        $c = CategoryRepository::getAllForShop($shopId);
        $arr = [];
        foreach ($c as $d) {
            $arr[$d['id']] = $d;
        }

        foreach ($arr as $k=>$v) {
            $pTitle = isset($v['parent_id']) ? $arr[$v['parent_id']]['title'] : '';
            $arr[$k]['parent_title'] = $pTitle;
        }
        return $arr;
    }

    public static function deleteAllForShop(int $shopId):void
    {
        CategoryRepository::delete([['shop_id', '=', $shopId]]);
    }
}
