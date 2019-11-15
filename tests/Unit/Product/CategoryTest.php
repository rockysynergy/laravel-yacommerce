<?php

namespace Tests\YaCommerce\Unit\Product;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\Category;

class CategoryTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function longTitleThrowsException()
    {
        $this->expectExceptionCode(1565331955);
        $category = new Category();
        $category->setTitle($this->makeStr(101));
    }

    /**
     * @test
     */
    public function longPicThrowsException()
    {
        $this->expectExceptionCode(1565332035);
        $category = new Category();
        $category->setPic($this->makeStr(121));
    }

    /**
     * @test
     */
    public function illegalParentIdThrowsException()
    {
        $this->expectExceptionCode(1565332088);
        $category = new Category();
        $category->setParentId(-2);
    }

    /**
     * @test
     */
    public function illegalShopIdThrowsException()
    {
        $this->expectExceptionCode(1565332125);
        $category = new Category();
        $category->setShopId(-2);
    }

    /**
     * @test
     */
    public function getPersistentData()
    {
        $data = ['id'=>2, 'title'=>'卫浴用品', 'pic'=>'path/to/category_pic.jpg', 'parent_id'=>2, 'shop_id'=>1];
        $category = ModelFactory::make(Category::class, $data);

        $this->assertEquals($data, $category->getPersistData());
    }
}
