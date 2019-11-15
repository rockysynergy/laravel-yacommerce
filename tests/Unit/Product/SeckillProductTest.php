<?php

namespace Tests\YaCommerce\Unit\Product;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Product\Model\InventoryException;
use Orq\Laravel\YaCommerce\Product\Model\SeckillProduct;

class SeckillProductTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function longTitleThrowsException()
    {
        $this->expectExceptionCode(1565332430);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setTitle($this->makeStr(101));
    }

    /**
     * @test
     */
    public function longCoverPicThrowsException()
    {
        $this->expectExceptionCode(1565332481);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setCoverPic($this->makeStr(301));
    }

    /**
     * @test
     */
    public function longDescriptionThrowsException()
    {
        $this->expectExceptionCode(1565332518);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setDescription($this->makeStr(2001));
    }

    /**
     * @test
     */
    public function longPicturesThrowsException()
    {
        $this->expectExceptionCode(1565332604);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setPictures($this->makeStr(501));
    }

    /**
     * @test
     */
    public function illegalPriceThrowsException()
    {
        $this->expectExceptionCode(1566283361);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setPrice(-12);
    }

    /**
     * @test
     */
    public function illegalCategoryIdThrowsException()
    {
        $this->expectExceptionCode(1565332642);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setCategoryId(-12);
    }

    /**
     * @test
     */
    public function illegalInventoryThrowsException()
    {
        $this->expectExceptionCode(1565744941);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setInventory(-12);
    }

    /**
     * @test
     */
    public function illegalTotalThrowsException()
    {
        $this->expectExceptionCode(1566282894);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setTotal(-12);
    }

    /**
     * @test
     */
    public function illegalSkPriceThrowsException()
    {
        $this->expectExceptionCode(1566282926);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setSkPrice(-12);
    }

    /**
     * @test
     */
    public function shortStartTimeThrowsException()
    {
        $this->expectExceptionCode(1566282824);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setStartTime($this->makeStr(18));
    }

    /**
     * @test
     */
    public function longStartTimeThrowsException()
    {
        $this->expectExceptionCode(1566282832);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setStartTime($this->makeStr(20));
    }

    /**
     * @test
     */
    public function shortEndTimeThrowsException()
    {
        $this->expectExceptionCode(1566282839);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setEndTime($this->makeStr(18));
    }

    /**
     * @test
     */
    public function longEndTimeThrowsException()
    {
        $this->expectExceptionCode(1566282846);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setEndTime($this->makeStr(20));
    }

    /**
     * @test
     */
    public function illegalStatusThrowsException()
    {
        $this->expectExceptionCode(1565840760);
        $seckillProduct = new SeckillProduct();
        $seckillProduct->setStatus(-1);
    }

    /**
     * @test
     */
    public function getSold()
    {
        $skProd = new SeckillProduct();
        $skProd->setTotal(30);
        $skProd->setInventory(18);
        $this->assertEquals(12, $skProd->getSold());
    }

    /**
     * @test
     */
    public function decInventoryThrowsExceptionWhenNotEnough()
    {
        $this->expectException(InventoryException::class);
        $this->expectExceptionCode(1566283551);
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3, 'sold'=>0];
        $prod = ModelFactory::make(SeckillProduct::class, $data);
        $prod->setStartTime(date('Y-m-d H:i:s', strtotime('- 3 hour')));
        $prod->setEndTime(date('Y-m-d H:i:s', strtotime('+ 7 hour')));

        $prod->decInventory(4);
    }

    /**
     * @test
     */
    public function decInventory()
    {
        $data = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>5, 'sold'=>0];
        $prod = ModelFactory::make(SeckillProduct::class, $data);
        $prod->setStartTime(date('Y-m-d H:i:s', strtotime('- 3 hour')));
        $prod->setEndTime(date('Y-m-d H:i:s', strtotime('+ 7 hour')));

        $prod->decInventory(4);
        $this->assertEquals(1, $prod->getInventory());
        $prod->decInventory(1);
        $this->assertEquals(0, $prod->getInventory());
    }

    /**
     * @test
     */
    public function getStatusTextForComingActivity()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('+ 3 hour')));
        $skProduct->setEndTime(date('Y-m-d H:i:s', strtotime('+ 7 hour')));
        $skProduct->setInventory(3);
        $this->assertEquals('即将开始', $skProduct->getStatusText());
    }

    /**
     * @test
     */
    public function getStatusTextForSoldOutActivity()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('-5 hour')));
        $skProduct->setEndTime(date('Y-m-d H:i:s', strtotime('+3 hour')));
        $skProduct->setInventory(0);
        $this->assertEquals('已抢光', $skProduct->getStatusText());
    }

    /**
     * @test
     */
    public function getStatusTextForAliveActivity()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('-5 hour')));
        $skProduct->setEndTime(date('Y-m-d H:i:s', strtotime('+3 hour')));
        $skProduct->setInventory(10);
        $this->assertEquals('立刻抢', $skProduct->getStatusText());
    }

    /**
     * @test
     */
    public function getProgress()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('-5 hour')));
        $skProduct->setEndTime(date('Y-m-d H:i:s', strtotime('+3 hour')));
        $skProduct->setTotal(100);
        $skProduct->setInventory(30);
        $this->assertEquals(70, $skProduct->getProgress());
    }

    /**
     * @test
     */
    public function getProgressForDiedActivity()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('-5 hour')));
        $skProduct->setEndTime(date('Y-m-d H:i:s', strtotime('-3 hour')));
        $skProduct->setTotal(100);
        $skProduct->setInventory(30);
        $this->assertEquals(100, $skProduct->getProgress());
    }

    /**
     * @test
     */
    public function isFinished()
    {
        $skProduct = new SeckillProduct();
        $skProduct->setStartTime(date('Y-m-d H:i:s', strtotime('-5 hour')));
        $endTime = date('Y-m-d H:i:s', strtotime('-3 hour'));
        $skProduct->setEndTime($endTime);

        $this->assertTrue($skProduct->isFinished());
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
            'inventory'=>10,
            'status'=>1,
            'start_time'=>'2019-08-20 13:30:14',
            'end_time'=>'2019-08-23 13:30:14',
            'sk_price'=>2400,
            'total'=>20,
        ];
        $seckillProduct = ModelFactory::make(SeckillProduct::class, $data);

        $this->assertEquals($data, $seckillProduct->getPersistData());
    }

    /**
     * @test
     */
    public function getDataWithInclude()
    {
        $data = [
            'id'=>2,
            'title'=>'电水壶zzzzz',
            'cover_pic'=>'/path/to/coverPic.jpg',
            'description'=>'商品详情描述',
            'price'=>5432,
            'pictures'=>'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id'=>3,
            'inventory'=>10,
            'status'=>1,
            'start_time'=>'2019-08-20 13:30:14',
            'end_time'=>'2019-08-23 13:30:14',
            'sk_price'=>2400,
            'total'=>20,
        ];
        $seckillProduct = ModelFactory::make(SeckillProduct::class, $data);

        $re = $seckillProduct->getData(['id', 'price']);
        $this->assertEquals($data['id'], $re['id']);
        $this->assertEquals($data['price'], $re['price']);
    }

    /**
     * @test
     */
    public function getDataIncludeFieldsNeedToBeDerived()
    {
        $data = [
            'id'=>2,
            'title'=>'电水壶zzzzz',
            'cover_pic'=>'/path/to/coverPic.jpg',
            'description'=>'商品详情描述',
            'price'=>5432,
            'pictures'=>'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id'=>3,
            'inventory'=>10,
            'status'=>1,
            'start_time'=>'2019-08-20 13:30:14',
            'end_time'=>date('Y-m-d H:i:s', strtotime('+3 hour')),
            'sk_price'=>2400,
            'total'=>20,
        ];
        $seckillProduct = ModelFactory::make(SeckillProduct::class, $data);

        $re = $seckillProduct->getData(['id', 'price', 'is_finished', 'status_text']);
        $this->assertEquals($data['id'], $re['id']);
        $this->assertEquals($data['price'], $re['price']);
        $this->assertFalse($re['is_finished']);
        $this->assertEquals('立刻抢', $re['status_text']);
    }

    /**
     * @test
     */
    public function getDataWithExclude()
    {
        $data = [
            'id'=>2,
            'title'=>'电水壶zzzzz',
            'cover_pic'=>'/path/to/coverPic.jpg',
            'description'=>'商品详情描述',
            'price'=>5432,
            'pictures'=>'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id'=>3,
            'inventory'=>10,
            'status'=>1,
            'start_time'=>'2019-08-20 13:30:14',
            'end_time'=>'2019-08-23 13:30:14',
            'sk_price'=>2400,
            'total'=>20,
        ];
        $seckillProduct = ModelFactory::make(SeckillProduct::class, $data);

        $re = $seckillProduct->getData([], ['id', 'price']);
        $this->assertFalse(\array_key_exists('id', $re));
        $this->assertFalse(\array_key_exists('price', $re));
        $this->assertEquals($data['description'], $re['description']);
    }
}
