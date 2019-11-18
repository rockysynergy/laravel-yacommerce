<?php

namespace Tests\YaCommerce\Functional\Product;


use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Product\Service\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function getByProductId()
    {
        $shop = [
            'id' => 3,
            'name' => '积分商城'
        ];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $product = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4];
        DB::table('yac_products')->insert($product);

        $result = ProductService::getById(3);
        $this->assertEquals($product['title'], $result['title']);
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionWhenNotEnough()
    {
        $this->expectExceptionCode(1565744840);
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $product = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3];
        DB::table('yac_products')->insert($product);

        ProductService::decInventory(3, 4, 'shop');
    }

    /**
     * @test
     */
    public function decInventory()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $product = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3];
        DB::table('yac_products')->insert($product);

        ProductService::decInventory(3, 3, 'shop');
        $d = DB::table('yac_products')->where('id', 3)->first();
        $this->assertEquals(0, $d->inventory);
    }

    /**
     * @test
     */
    public function incInventoryForProducts()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $product_1 = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3];
        $product_2 = ['id'=>6, 'title'=>'羽毛', 'category_id'=>4, 'inventory'=>2];
        DB::table('yac_products')->insert([$product_1, $product_2]);

        ProductService::incInventoryForProducts([[3,2],[6, 5]], 'shop');
        $d = DB::table('yac_products')->where('id', 3)->first();
        $this->assertEquals(5, $d->inventory);

        $d = DB::table('yac_products')->where('id', 6)->first();
        $this->assertEquals(7, $d->inventory);
    }
}
