<?php

namespace Tests\YaCommerce\Functional\Shop\Repository;

use Orq\Laravel\YaCommerce\Shop\Repository\ShopRepository;
use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Shop\Service\ShopService;

class ShopServiceTest extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function getAllProductsForShop()
    {
        $shop = [
            'id' => 3,
            'name' => '积分商城',
            'type' => 'bp_shop',
        ];
        DB::table('yac_shops')->insert($shop);
        $categories = [
            ['id'=> 2, 'title'=>'生活用品', 'shop_id'=>3],
            ['id'=> 4, 'title'=>'文体用品', 'shop_id'=>3]
        ];
        DB::table('yac_categories')->insert($categories);
        $products = [
            ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4],
            ['id'=>5, 'title'=>'英雄钢笔', 'category_id'=>4],
            ['id'=>7, 'title'=>'电热水壶', 'category_id'=>2],
        ];
        DB::table('yac_products')->insert($products);

        $result = ShopService::getAllProductsForShop(3, 'bp_shop');
        $this->assertEquals(3, count($result));
        foreach ($result as $re) {
            if ($re->getId() == 7) {
                $this->assertEquals($products[2]['title'], $re->getTitle());
            }
        }
    }

    /**
     * @test
     */
    public function createShop()
    {
        $name = '积分商城';
        $sId = ShopService::createShop($name);

        $shop = ShopRepository::findById($sId, true);
        $this->assertEquals($name, $shop->getName());

        $category = DB::table('yac_categories')->get()->get(0);
        $this->assertEquals($shop->getId(), $category->shop_id);
    }
}
