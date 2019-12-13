<?php

namespace Tests\YaCommerce\Unit\Product;

use Orchestra\Testbench\TestCase;;

use Tests\MakeStringTrait;
use Orq\Laravel\YaCommerce\Product\Model\Category;

class CategoryTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function negativePidThrowsException()
    {
        $this->expectExceptionCode(1573629695);
        $data = ['id' => 2, 'title' => '卫浴用品', 'pic' => 'path/to/category_pic.jpg', 'parent_id' => -2, 'shop_id' => 1];
        $category = new Category();
        $category->validate($data);
    }

}
