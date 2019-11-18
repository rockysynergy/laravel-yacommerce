<?php

namespace Tests\YaCommerce\Functional\Product;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Product\Service\CategoryService;

class CategoryServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function getAllForShop()
    {
        $shop = [ 'id' => 3,'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $pCategory = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($pCategory);
        $category = ['id'=> 6, 'title'=>'家居用品', 'shop_id'=>3, 'parent_id'=>4];
        DB::table('yac_categories')->insert($category);

        $result = CategoryService::getAllForShop(3);
        $this->assertEquals(2, count($result));
        $this->assertEquals($pCategory['title'], $result[6]['parent_title']);
    }
}
