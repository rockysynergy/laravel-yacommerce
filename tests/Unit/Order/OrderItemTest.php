<?php

namespace Tests\YaCommerce\Unit\Order;

use Orchestra\Testbench\TestCase;;

use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderItem;

class OrderItemTest extends TestCase
{
    use MakeStringTrait;

    /**
     * @test
    */
    public function illegalOrderIdThrowsException()
    {
        $this->expectExceptionCode(1564735291);
        $item = new OrderItem();
        $item->setOrderId(-1);
    }

    /**
     * @test
    */
    public function longThumbnailThrowsException()
    {
        $this->expectExceptionCode(1564735346);
        $item = new OrderItem();
        $item->setThumbnail($this->makeStr(201));
    }

    /**
     * @test
    */
    public function longTitleThrowsException()
    {
        $this->expectExceptionCode(1564735384);
        $item = new OrderItem();
        $item->setTitle($this->makeStr(201));
    }

    /**
     * @test
    */
    public function longInfoThrowsException()
    {
        $this->expectExceptionCode(1564735423);
        $item = new OrderItem();
        $item->setInfo($this->makeStr(201));
    }

    /**
     * @test
    */
    public function negativeAmountThrowsException()
    {
        $this->expectExceptionCode(1564735559);
        $item = new OrderItem();
        $item->setAmount(-1);
    }

    /**
     * @test
    */
    public function nonNumericUnitPriceThrowsException()
    {
        $this->expectExceptionCode(1564735709);
        $item = new OrderItem();
        $item->setUnitPrice('aa');
    }

    /**
     * @test
    */
    public function nonNumericPayAmountThrowsException()
    {
        $this->expectExceptionCode(1564735762);
        $item = new OrderItem();
        $item->setPayAmount('aa');
    }

    /**
     * @test
     */
    public function getPersistData()
    {
        $data = [
            'id'=>1,
            'order_id'=>1,
            'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
            'title'=>'会费',
            'info'=>'2019-07-01至2019-10-31(3个月)会费',
            'amount'=>1,
            'pay_amount'=>388.34,
            'unit_price'=>40,
        ];
        $item = ModelFactory::make(OrderItem::class, $data);
        $this->assertEquals($data, $item->getPersistData());
    }

}
