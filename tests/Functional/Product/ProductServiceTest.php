<?php

namespace Tests\YaCommerce\Functional\Product;


use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Domain\Product\Service\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

class ProductServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);

        $productService = new ProductService('product');
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        $productService->create($pData);

        $this->assertDatabaseHas('yac_products', $pData);
    }


    /**
     * @test
     */
    public function getProductById()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($pData);

        $productService = new ProductService('product');
        $result = $productService->findById($pData['id']);

        $this->assertEquals($pData['title'], $result->title);
    }

    /**
     * @test
     */
    public function getVariantById()
    {
        $this->assertEquals('a', 'b');
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionWhenNoProductCanFind()
    {
        $this->expectExceptionCode(1576485686);

        $productService = new ProductService('product');
        $productService->decInventory(3, 2);
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
        $product = [
            'id' => 2,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 30,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($product);
        $productService = new ProductService('product');
        $productService->decInventory($product['id'], 2);

        $prod = Product::find($product['id']);
        $this->assertEquals(28, $prod->inventory);
    }

    /**
     * @test
     */
    public function incInventoryThrowsExceptionWhenNoProductCanFind()
    {
        $this->expectExceptionCode(1576486738);

        $productService = new ProductService('product');
        $productService->incInventory(3, 2);
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
        $product = [
            'id' => 2,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 30,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($product);
        $productService = new ProductService('product');
        $productService->incInventory($product['id'], 2);

        $prod = Product::find($product['id']);
        $this->assertEquals(32, $prod->inventory);
    }
}
