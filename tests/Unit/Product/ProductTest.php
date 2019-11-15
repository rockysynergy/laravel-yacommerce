<?php

namespace Tests\YaCommerce\Unit\Product;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Product\Model\Product;

class ProductTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function longTitleThrowsException()
    {
        $this->expectExceptionCode(1565332430);
        $product = new Product();
        $product->setTitle($this->makeStr(101));
    }

    /**
     * @test
     */
    public function longCoverPicThrowsException()
    {
        $this->expectExceptionCode(1565332481);
        $product = new Product();
        $product->setCoverPic($this->makeStr(301));
    }

    /**
     * @test
     */
    public function longDescriptionThrowsException()
    {
        $this->expectExceptionCode(1565332518);
        $product = new Product();
        $product->setDescription($this->makeStr(2001));
    }

    /**
     * @test
     */
    public function longPicturesThrowsException()
    {
        $this->expectExceptionCode(1565332604);
        $product = new Product();
        $product->setPictures($this->makeStr(501));
    }

    /**
     * @test
     */
    public function longParametersThrowsException()
    {
        $this->expectExceptionCode(1570606501);
        $product = new Product();
        $product->setParameters($this->makeStr(3001));
    }

    /**
     * @test
     */
    public function setParameters()
    {
        $product = new Product();
        $product->setParameters('长：3cm，宽：20cm');

        $data = $product->getPersistData();
        $this->assertEquals(json_encode(['长：3cm', '宽：20cm']), $data['parameters']);
    }

    /**
     * @test
     */
    public function getParameters()
    {
        $product = new Product();
        $product->setParameters('长：3cm，宽：20cm');

        $params = $product->getParameters();
        $this->assertEquals(['长：3cm', '宽：20cm'], $params);
    }

    /**
     * @test
     */
    public function illegalPriceThrowsException()
    {
        $this->expectExceptionCode(1565332557);
        $product = new Product();
        $product->setPrice(-12);
    }

    /**
     * @test
     */
    public function illegalCategoryIdThrowsException()
    {
        $this->expectExceptionCode(1565332642);
        $product = new Product();
        $product->setCategoryId(-12);
    }

    /**
     * @test
     */
    public function illegalInventoryThrowsException()
    {
        $this->expectExceptionCode(1565744941);
        $product = new Product();
        $product->setInventory(-12);
    }

    /**
     * @test
     */
    public function illegalStatusThrowsException()
    {
        $this->expectExceptionCode(1565840760);
        $product = new Product();
        $product->setStatus(-1);
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionWhenNotEnough()
    {
        $this->expectException(InventoryException::class);
        $this->expectExceptionCode(1565744840);
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3];
        $prod = ModelFactory::make(Product::class, $data);

        $prod->decInventory(4);
    }

    /**
     * @test
     */
    public function decInventory()
    {
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>5];
        $prod = ModelFactory::make(Product::class, $data);

        $prod->decInventory(4);
        $this->assertEquals(1, $prod->getInventory());
        $prod->decInventory(1);
        $this->assertEquals(0, $prod->getInventory());
    }

    /**
     * @test
     */
    public function getPersistentData()
    {
        $data = [
            'id'=>2,
            'title'=>'电水壶zzzzz',
            'cover_pic'=>'/path/to/coverPic.jpg',
            'description'=>'商品详情描述',
            'price'=>5432,
            'pictures'=>'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id'=>3,
            'inventory'=>0,
            'status'=>1,
        ];
        $bData = $data;
        $bData['parameters'] = json_encode(['width'=>32, 'height'=>33]);
        $bData['show_price'] = $data['price'];
        $product = ModelFactory::make(Product::class, $bData);

        $this->assertEquals($bData, $product->getPersistData());
    }
}
