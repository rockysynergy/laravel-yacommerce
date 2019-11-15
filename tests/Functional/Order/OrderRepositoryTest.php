<?php

namespace Tests\YaCommerce\Functional\Order;


use Tests\DbTestCase;
use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Order\Model\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Order\Repository\OrderRepository;

class OrderRepositoryTest extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function findById()
    {
        $data = [
            'id'=>1,
            'pay_amount'=>23.34,
            'pay_status'=>1,
            'order_number'=>'HF20190212143423008',
            'pay_method'=>1,
            'user_id'=>23,
            'pid'=>1,
            'ptype'=>'app'
        ];
        DB::table('yac_orders')->insert($data);


        $result = OrderRepository::findById(1);
        $this->assertEquals($data['id'], $result['id']);
        $this->assertEquals(0, count($result['items']));
    }

    /**
     * @test
     */
    public function saveOrderItems()
    {
        $data = ['user_id'=>3, 'pid'=>1, 'ptype'=>'app'];
        $order = ModelFactory::make(Order::class, $data, ['HF']);
        $this->assertRegExp('/^HF*/', $order->getOrderNumber());
        $orderId = OrderRepository::saveGetId($order);

        $item = [
            'thumbnail'=>'storage/iamges/hf_default.jpg',
            'title'=>'商智联盟会费支付',
            'info'=>'2019-07-23至2019-10-23会费',
            'amount'=>1,
            'unit_price'=>30.00,
            'pay_amount'=>87.99,
        ];
        OrderRepository::saveOrderItems($orderId, [$item]);

        $rOrder = OrderRepository::findById($orderId);
        $this->assertEquals(1, count($rOrder['items']));
        $this->assertEquals($item['thumbnail'], $rOrder['items'][0]['thumbnail']);
    }


    /**
     * @test
     */
    public function saveOrderItemsAndGetOrderAsObj()
    {
        $data = ['user_id'=>3, 'pid'=>1, 'ptype'=>'app'];
        $order = ModelFactory::make(Order::class, $data, ['HF']);
        $this->assertRegExp('/^HF*/', $order->getOrderNumber());
        $orderId = OrderRepository::saveGetId($order);

        $item = [
            'thumbnail'=>'storage/iamges/hf_default.jpg',
            'title'=>'商智联盟会费支付',
            'info'=>'2019-07-23至2019-10-23会费',
            'amount'=>1,
            'unit_price'=>30.00,
            'pay_amount'=>87.99,
        ];
        OrderRepository::saveOrderItems($orderId, [$item]);

        $rOrder = OrderRepository::findById($orderId, true);
        $this->assertEquals(1, count($rOrder->getItems()));
        $this->assertEquals($item['thumbnail'], ($rOrder->getItems()[0])->getThumbnail());
    }

    /**
     * @test
     */
    public function findByOrderNumber()
    {
        $data = ['user_id'=>3, 'pid'=>4, 'ptype'=>'bp_shop'];
        $order = ModelFactory::make(Order::class, $data, ['HF']);
        $this->assertRegExp('/^HF*/', $order->getOrderNumber());
        OrderRepository::save($order);
        $this->assertDatabaseHas('yac_orders', $data);

        $order = OrderRepository::findByOrderNumber($order->getOrderNumber());
        $this->assertEquals($data['user_id'], $order->getUserId());
    }

}
