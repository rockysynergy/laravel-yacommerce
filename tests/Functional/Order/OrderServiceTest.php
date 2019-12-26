<?php

namespace Tests\YaCommerce\Functional\Order\Repository;


use Tests\DbTestCase;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\Order\PrepaidUserInterface;
use Orq\Laravel\YaCommerce\Domain\Order\Service\OrderService;

class OrderServiceTest  extends DbTestCase
{
    use RefreshDatabase;
    use MakeStringTrait;

    protected function getPackageProviders($app)
    {
        return ['Orq\Laravel\YaCommerce\YaCommerceServiceProvider'];
    }

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
        App::setlocale('zh_CN');
        $data = [
            'user_id' => 333,
            'shop_id' => 533,
            // 'order_number_prefix' => 'SK',
            'order_number' => 'SK201912230923453213',
            'order_items'=> [[
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'会费',
                'info'=>'2019-07-01至2019-10-31(3个月)会费',
                'amount'=>1,
                'pay_amount'=>388,
                'unit_price'=>40,
                'product_id' => 987,
            ]],
        ];
        // Create method needs to calculate total and create orderItems
        $orderService = new OrderService(new Order());
        $orderService->create($data);
        $this->assertDatabaseHas('yac_orders', ['user_id' => $data['user_id'], 'shop_id' => $data['shop_id']]);
        $orderItem = $data['order_items'][0];
        $this->assertDatabaseHas('yac_orderitems', ['title' => $orderItem['title'], 'pay_amount' => $orderItem['pay_amount']]);
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
     * @test
     */
    public function makePrePaidOrderThrowsExceptionIfLeftCreditIsNotEnough()
    {
        $this->expectExceptionCode(1565581699);
        $data = [
            'user_id'=>3,
            'type' => 'prepay',
            'order_number_prefix' => 'BP',
            'order_items'=> [[
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

        $orderService = new OrderService();
        $orderService->makeOrder($data, $user);
    }

    /**
     * @test
     */
    public function makePrePaidOrderSavesTheOrderInformation()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 23,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 11,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($pData);
        $data = [
            'user_id'=>3,
            'type' => 'prepay',
            'order_number_prefix' => 'BP',
            'order_items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'电水壶',
                'info'=>'2019最新款',
                'amount'=>$pData['inventory'] - 2,
                'pay_amount'=>3800,
                'unit_price'=>3,
                'product_id' => $pData['id'],
            ]],
            'shop_id' => $shop['id'],
        ];
        $user = new PrapayUserA(50);

        $orderService = new OrderService();
        $orderService->makeOrder($data, $user);
        $orderItem = $data['order_items'][0];
        $this->assertDatabaseHas('yac_orderitems', ['title' => $orderItem['title'], 'unit_price' => $orderItem['unit_price']]);
        $this->assertEquals(12, $user->getLeftCredit());

        $product = DB::table('yac_products')->where('id', '=', $pData['id'])->first();
        $this->assertEquals(2, $product->inventory);
    }

    /**
     * @test
     */
    public function saveOrderThenGetInfoForWxPay()
    {
        $this->markTestIncomplete('Needs to interact with WxPay server');
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
     * @test
     */
    public function getOrdersForUser()
    {
        $data_a = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'b',
            'pay_method'=>1,
            'user_id'=>3,
            'shop_id'=>2,
            'pay_amount'=>355,
        ];
        $data_b = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'c',
            'pay_method'=>1,
            'user_id'=>3,
            'shop_id'=>3,
            'pay_amount'=>300,
        ];
        $data_c = [
            'order_number'=>substr($this->makeStr(21), 0, 19).'d',
            'pay_method'=>1,
            'user_id'=>4,
            'shop_id'=>3,
            'pay_amount'=>320,
        ];
        DB::table('yac_orders')->insert([$data_a, $data_b, $data_c]);

        $re = DB::table('yac_orders')->get();
        $this->assertEquals(3, $re->count());

        $orderService = new OrderService();
        $re = $orderService->findAllForUser(['shop_id' => 2, 'user_id' => 3]);
        $this->assertEquals(1, $re->count());
        $this->assertEquals($data_a['pay_amount'], $re[0]['pay_amount']);
    }


    /**
     * @test
     */
    public function saveOrderSavesTheShipaddressIfTheDataHasOne()
    {
        $this->markTestSkipped('Depends on shipment module');
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 23,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 11,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($pData);
        $data = [
            'user_id'=>3,
            'type' => 'prepay',
            'order_number_prefix' => 'BP',
            'order_items'=> [[
                'id'=>1,
                'thumbnail'=>'https://wqb.fs007.com.cn/storage/images/mythumb.jpg',
                'title'=>'电水壶',
                'info'=>'2019最新款',
                'amount'=>10,
                'pay_amount'=>3800,
                'unit_price'=>3,
                'product_id' => $pData['id'],
            ]],
            'shipaddress'=>[
                'name'=>'潘耶林',
                'mobile'=>'13977822812',
                'address'=>'广东省江门市东大街32号'
            ],
            'shop_id'=>1,
        ];
        $user = new PrapayUserA(50);
        $orderService = new OrderService();
        $orderService->makeOrder($data, $user);

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
