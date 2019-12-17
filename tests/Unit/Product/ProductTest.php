<?php

namespace Tests\YaCommerce\Unit\Product;

use Tests\MakeStringTrait;

use Orchestra\Testbench\TestCase;;

use Illuminate\Support\Facades\App;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

class ProductTest extends TestCase
{
    use MakeStringTrait;

    protected function getPackageProviders($app)
    {
        return ['Orq\Laravel\YaCommerce\YaCommerceServiceProvider'];
    }

    /**
     * @test
     */
    public function missingTitleThrowsException()
    {
        $this->expectExceptionCode(1573629695);
        $data = [
            'id' => 2,
            'cover_pic' => '/path/to/coverPic.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => 3,
            'inventory' => 0,
            'status' => 1,
        ];
        $product = new Product();
        $product->validate($data);
        $this->assertTrue(false);
    }

    /**
     * @test
     */
    public function longTitleThrowsException()
    {
        $this->expectExceptionCode(1573629695);
        $data = [
            'id' => 2,
            'title' => $this->makeStr(101),
            'cover_pic' => '/path/to/coverPic.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => 3,
            'inventory' => 0,
            'status' => 1,
        ];
        $product = new Product();
        $product->validate($data);
        $this->assertTrue(false);
    }

    /**
     * @test
     */
    public function missingCoverPicThrowsException()
    {
        $this->expectExceptionCode(1573629695);
        $data = [
            'id' => 2,
            'title' => 'The product title',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => 3,
            'inventory' => 0,
            'status' => 1,
        ];
        $product = new Product();
        $product->validate($data);
        $this->assertTrue(false);
    }

    /**
     * @test
     */
    public function longCoverPicThrowsException()
    {
        App::setlocale('zh_CN');
        $this->expectExceptionCode(1573629695);
        $this->expectExceptionMessage('封面图片不能超过300个字');
        $data = [
            'id' => 2,
            'title' => $this->makeStr(2),
            'cover_pic' => $this->makeStr(301),
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => 3,
            'inventory' => 0,
            'status' => 1,
        ];
        $product = new Product();
        $product->validate($data);
        $this->assertTrue(false);
    }

    /**
     * @test
     */
    public function nonpositiveNumberForDecInventoryThrowsException()
    {
        $this->expectExceptionCode(1576138822);
        $product = new Product();
        $product->decInventory(0);
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionIfInventoryIsNotEnough()
    {
        $this->expectExceptionCode(1565744840);
        $product = new Product();
        $product->inventory = 30;
        $product->decInventory(31);
    }

    /**
     * @test
     */
    public function nonpositiveNumberForIncInventoryThrowsException()
    {
        $this->expectExceptionCode(1576138822);
        $product = new Product();
        $product->decInventory(0);
    }

}
