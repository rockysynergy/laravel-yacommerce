<?php

namespace Tests\YaCommerce\Functional\Product;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Product\Model\Category;

class CategoryTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function getAllForShopBuildTheCategoryTree()
    {
        $shop = [ 'id' => 3,'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $pCat = Category::create(['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3]);
        $bCat = Category::create(['id'=> 6, 'title'=>'家居用品', 'shop_id'=>3, 'parent_id'=>4]);
        $pCat->appendNode($bCat);

        $cat = new Category();
        $result = $cat->getAllForShop(3);
        $this->assertEquals(1, $result->count());
        $this->assertEquals('家居用品', $result->get(0)->children->get(0)->title);
    }

    /**
     * @test
     */
    public function createNewRootCategory()
    {
        $shop = [ 'id' => 3,'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);

        $category = new Category();
        $data = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        $category->createNew($data);

        $tree = $category->getAllForShop(3);
        $this->assertEquals(0, $tree->get(0)->children->count());
    }

    /**
     * @test
     */
    public function createNewChildrenCategory()
    {
        $shop = [ 'id' => 3,'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);

        $category = new Category();
        $data = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        Category::create($data);
        $data = ['id'=> 6, 'title'=>'家居用品', 'shop_id'=>3, 'parent_id'=>4];
        $category->createNew($data);

        $tree = $category->getAllForShop(3);
        $this->assertEquals(1, $tree->get(0)->children->count());
        $this->assertEquals('家居用品', $tree->get(0)->children->get(0)->title);
    }
}
