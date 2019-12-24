<?php

namespace Tests\YaCommerce\Functional\Order\Repository;


use Tests\DbTestCase;
use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Domain\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Service\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Tests\MakeStringTrait;

class OrderServiceTest  extends DbTestCase
{
    use RefreshDatabase;
    use MakeStringTrait;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('yac.product_service.bp_shop', 'ProductService');
        $app['config']->set('yac.product_service.shop', 'ProductService');
        $app['config']->set('yac.product_service.seckill', 'SeckillProductService');
        parent::getEnvironmentSetUp($app);
    }

    /**
     * @test
     */
    public function create()
    {
        $data = [
            'user_id' => 333,
            'shop_id' => 533,
            'order_number_prefix' => 'SK',
            'order_items'=> [[
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'会费',
                'info'=>'2019-07-01至2019-10-31(3个月)会费',
                'amount'=>1,
                'pay_amount'=>388,
                'unit_price'=>40,
            ]],
        ];
        // Create method needs to calculate total and create orderItems
        $orderService = new OrderService(new Order());
        $orderService->create($data);
        $this->assertDatabaseHas('yac_orders', ['user_id' => $data['user_id'], 'shop_id' => $data['shop_id']]);
        $this->assertDatabaseHas('yac_orderitems', $data['order_items'][0]);
    }

    

    /**
     * @test
     */
    public function update()
    {

        $data = [
            'id' => 989,
            'user_id' => 333,
            'shop_id' => 533,
            'pay_status' => 1,
            'pay_method' => 2,
            'pay_amount' => 787,
            'order_number' => 'SK201912092304331234',
        ];
        DB::table('yac_orders')->insert($data);
        $item = [
            'id' => 99,
            'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
            'title'=>'会费',
            'info'=>'2019-07-01至2019-10-31(3个月)会费',
            'amount'=>1,
            'pay_amount'=>388,
            'unit_price'=>40,
            'order_id' => $data['id'],
        ];
        DB::table('yac_orderitems')->insert($item);

        $uData = $data;
        $uData['pay_amount'] = 778;
        $uItem = $item;
        $uItem['pay_amount'] = 434;
        $uData['order_items'] = [$uItem];
        $orderService = new OrderService(new Order());
        $orderService->update($uData);
        $this->assertDatabaseHas('yac_orders', ['user_id' => $data['user_id'], 'pay_amount' => $uData['pay_amount']]);
        $this->assertDatabaseHas('yac_orderitems', $uItem);
    }

    /**
     */
    public function saveOrderThenGetInfoForWxPay()
    {
        $data = [
            'user_id'=>3,
            'items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'会费',
                'info'=>'2019-07-01至2019-10-31(3个月)会费',
                'amount'=>1,
                'pay_amount'=>388,
                'unit_price'=>40,
            ]],
            'ptype'=>'app',
            'pid'=>1,
        ];
        $openid = 'zjkaje3adjfakd';

        $id = OrderService::saveOrder($data, 'HF');
        $info = OrderService::getInfoForWxPayUnifiedOrder($id, $openid);

        $this->assertEquals('会费', $info['body']);
        $this->assertRegExp('/^HF.*/', $info['out_trade_no']);
        $this->assertEquals(388, $info['total_fee']);
    }

    /**
     */
    public function saveOrderDeductTheProductInventoryIfProductIdPresented()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $product = ['id'=>3, 'title'=>'羽毛球拍', 'category_id'=>4, 'inventory'=>3];
        DB::table('yac_products')->insert($product);
        $data = [
            'user_id'=>3,
            'items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'会费',
                'info'=>'2019-07-01至2019-10-31(3个月)会费',
                'amount'=>1,
                'pay_amount'=>388,
                'unit_price'=>40,
                'product_id'=>3
            ]],
            'ptype'=>'shop',
            'pid'=>1,
        ];

        OrderService::saveOrder($data, 'HF');
        $d = DB::table('yac_products')->where('id', 3)->first();
        $this->assertEquals(2, $d->inventory);
    }

    /**
     */
    public function getOrdersForUser()
    {
        $data_a = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'b',
            'pay_method'=>1,
            'user_id'=>3,
            'ptype'=>'app',
            'pid'=>2,
            'pay_amount'=>355,
        ];
        $data_b = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'c',
            'pay_method'=>1,
            'user_id'=>3,
            'ptype'=>'shop',
            'pid'=>3,
            'pay_amount'=>300,
        ];
        $data_c = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'d',
            'pay_method'=>1,
            'user_id'=>4,
            'ptype'=>'shop',
            'pid'=>3,
            'pay_amount'=>320,
        ];
        DB::table('yac_orders')->insert([$data_a, $data_b, $data_c]);

        $re = DB::table('yac_orders')->get();
        $this->assertEquals(3, $re->count());

        $re = OrderService::findAllForUser(3, 'app', 2);
        $this->assertEquals(1, count($re));
        $this->assertEquals($data_a['pay_amount'], $re[0]['pay_amount']);
    }

    /**
     */
    public function makePrePaidOrderThrowsExceptionIfLeftCreditIsNotEnough()
    {
        $this->expectExceptionCode(1565581699);
        $data = [
            'user_id'=>3,
            'items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'电水壶',
                'info'=>'2019最新款',
                'amount'=>10,
                'pay_amount'=>38800,
                'unit_price'=>40,
            ]]
        ];
        $user = new PrapayUserA(50);

        OrderService::makePrepaidOrder($data, $user);
    }

    /**
     */
    public function makePrePaidOrderSavesTheOrderInformation()
    {
        $data = [
            'user_id'=>3,
            'items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'电水壶',
                'info'=>'2019最新款',
                'amount'=>10,
                'pay_amount'=>3800,
                'unit_price'=>3,
            ]],
            'ptype'=>'shop',
            'pid'=>1,
        ];
        $user = new PrapayUserA(50);

        OrderService::makePrepaidOrder($data, $user);
        $this->assertDatabaseHas('yac_orderitems', $data['items'][0]);
        $this->assertEquals(12, $user->getLeftCredit());
    }

    /**
     */
    public function saveOrderSavesTheShipaddressIfTheDataHasOne()
    {
        $data = [
            'user_id'=>3,
            'items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'电水壶',
                'info'=>'2019最新款',
                'amount'=>10,
                'pay_amount'=>3800,
                'unit_price'=>3,
            ]],
            'shipaddress'=>[
                'name'=>'潘耶林',
                'mobile'=>'13977822812',
                'address'=>'广东省江门市东大街32号'
            ],
            'ptype'=>'shop',
            'pid'=>1,
        ];
        $user = new PrapayUserA(50);
        OrderService::makePrepaidOrder($data, $user);

        $this->assertDatabaseHas('yac_shipaddresses', $data['shipaddress']);
        $order = DB::table('yac_orders')->first();
        $this->assertTrue($order->shipaddress_id > 0);
    }
}

class PrapayUserA implements PrepaidUserInterface {
    protected $credit;

    public function __construct($credit=0)
    {
        $this->credit = $credit;
    }

    public function getLeftCredit()
    {
        return $this->credit;
    }

    public function deductCredit($amount)
    {
        $this->credit -= $amount;
    }
};
