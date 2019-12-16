<?php

namespace Tests\YaCommerce\Unit\Order;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\Order\Model\OrderItem;

class OrderTest extends TestCase
{
    use MakeStringTrait;

    /**
     * @test
     */
    public function illegalPtypeThrowsException()
    {
        $this->expectExceptionCode(1565851605);
        $order = new Order('HF');
        $order->setPtype($this->makeStr(16));
    }

    /**
     * @test
     */
    public function illegalPidThrowsException()
    {
        $this->expectExceptionCode(1565851783);
        $order = new Order('HF');
        $order->setPid(-1);
    }

    /**
     * @test
     */
    public function IllegalPayStatusThrowsException()
    {
        $this->expectExceptionCode(1564725830);
        $order = new Order('HF');
        $order->setPayStatus(-1);
    }

    /**
     * @test
     */
    public function illegalUserIdThrowsException()
    {
        $this->expectExceptionCode(1564793861);
        $order = new Order('HF');
        $order->setUserId(-1);
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function IllegalPayMethodThrowsException()
    {
        $this->expectExceptionCode(1564726085);
        $order = new Order('HF');
        $order->setPayMethod(-1);
    }

    /**
     * @test
     */
    public function shortOrderNoThrowsException()
    {
        $this->expectExceptionCode(1564725800);
        $order = new Order('HF');
        $order->setOrderNumber($this->makeStr(19));
    }

    /**
     * @test
     */
    public function longOrderNoThrowsException()
    {
        $this->expectExceptionCode(1564725814);
        $order = new Order('HF');
        $order->setOrderNumber($this->makeStr(22));
    }

    /**
     * @test
     */
    public function invalidShiptrackingIdThrowsException()
    {
        $this->expectExceptionCode(1571296443);
        $order = new Order('HF');
        $order->setShiptrackingId(-1);
    }

    /**
     * @test
     */
    public function longExorderNoThrowsException()
    {
        $this->expectExceptionCode(1564725821);
        $order = new Order('HF');
        $order->setExorderNumber($this->makeStr(31));
    }

    /**
     * @test
     */
    public function noNumericPayAmountThrowsException()
    {
        $this->expectExceptionCode(1564732892);
        $order = new Order('HF');
        $order->setPayAmount('aaa');
    }

    /**
     * @test
     */
    public function generateOrderNumberUponCreation()
    {
        $order = new Order('HF');
        $this->assertEquals('HF', substr($order->getOrderNumber(), 0, 2));
    }

    /**
     * @test
     */
    public function getPersistDataGenerateDefaultValues()
    {
        $data = [
            'pay_amount' => 32.55,
            'user_id' => 2,
            'shipaddress_id'=>3,
            'pid'=>3,
            'ptype'=>'app',
            'shiptracking_id'=> 12
        ];
        $order = ModelFactory::make(Order::class, $data, ['HF']);
        $re = $order->getPersistData();
        $this->assertEquals($data['pay_amount'], $re['pay_amount']);
        $this->assertEquals('1', $re['pay_status']);
        $this->assertEquals('1', $re['pay_method']);
        $this->assertTrue(isset($re['created_at']));
        $this->assertTrue(isset($re['updated_at']));
        $this->assertEquals(0, $re['deleted']);
        $this->assertEquals(3, $re['shipaddress_id']);
        $this->assertRegExp('/^HF.*/', $re['order_number']);

        $this->assertEquals($data['pid'], $re['pid']);
        $this->assertEquals($data['ptype'], $re['ptype']);

        $this->assertEquals($data['shiptracking_id'], $re['shiptracking_id']);
    }

    /**
     * @test
     */
    public function updatePayStatusChangesDataForPersistence()
    {
        $data = [
            'pay_amount' => 32.55,
            'user_id' => 5
        ];
        $order = ModelFactory::make(Order::class, $data, ['HF']);
        $re = $order->getPersistData();
        $this->assertEquals('1', $re['pay_status']);

        $order->setPayStatus(2);
        $re = $order->getPersistData();
        $this->assertEquals('2', $re['pay_status']);
    }

    /**
     * @test
     */
    public function getOrderDetail()
    {
        $order = new Order();
        $data = [
            'id' => 1,
            'thumbnail' => 'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
            'title' => '会费',
            'info' => '2019-07-01至2019-10-31(3个月)会费',
            'amount' => 1,
            'pay_amount' => 388.34,
            'unit_price' => 40,
        ];
        $item = ModelFactory::make(OrderItem::class, $data);
        $order->setItems([$item]);
        $this->assertEquals('会费', $order->getDescription());
    }
}
