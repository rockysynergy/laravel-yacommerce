<?php

namespace Tests\YaCommerce\Unit\Product;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Product\Model\ProductVariant;

class ProductVariantTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function longModelThrowsException()
    {
        $this->expectExceptionCode(1570608661);
        $product = new ProductVariant();
        $product->setModel($this->makeStr(101));
    }

    /**
     * @test
     */
    public function illegalProductIdThrowsException()
    {
        $this->expectExceptionCode(1570608626);
        $product = new ProductVariant();
        $product->setProductId(-1);
    }

    /**
     * @test
     */
    public function longPicturesThrowsException()
    {
        $this->expectExceptionCode(1570608830);
        $product = new ProductVariant();
        $product->setPictures($this->makeStr(501));
    }

    /**
     * @test
     */
    public function longParametersThrowsException()
    {
        $this->expectExceptionCode(1570608843);
        $product = new ProductVariant();
        $product->setParameters($this->makeStr(3001));
    }

    /**
     * @test
     */
    public function illegalPriceThrowsException()
    {
        $this->expectExceptionCode(1570608819);
        $product = new ProductVariant();
        $product->setPrice(-12);
    }

    /**
     * @test
     */
    public function illegalInventoryThrowsException()
    {
        $this->expectExceptionCode(1570609275);
        $product = new ProductVariant();
        $product->setInventory(-12);
    }

    /**
     * @test
     */
    public function illegalStatusThrowsException()
    {
        $this->expectExceptionCode(1570609239);
        $product = new ProductVariant();
        $product->setStatus(-1);
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionWhenNotEnough()
    {
        $this->expectException(InventoryException::class);
        $this->expectExceptionCode(1570609318);
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'product_id'=>4, 'inventory'=>3];
        $prod = ModelFactory::make(ProductVariant::class, $data);

        $prod->decInventory(4);
    }

    /**
     * @test
     */
    public function decInventory()
    {
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'product_id'=>4, 'inventory'=>5];
        $prod = ModelFactory::make(ProductVariant::class, $data);

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
            'model'=>'DX-320-2',
            'price'=>5432,
            'pictures'=>'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'product_id'=>3,
            'inventory'=>0,
            'status'=>1,
            'parameters'=>json_encode(['width'=>32, 'height'=>33]),
        ];
        $product = ModelFactory::make(ProductVariant::class, $data);
        $bData = $data;
        $bData['show_price'] = $data['price'];

        $this->assertEquals($bData, $product->getPersistData());
    }
}
