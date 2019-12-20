<?php

namespace Tests\YaCommerce\Functional\Campaign;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Domain\UserInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign;

class CampaignTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function addAndGetProducts()
    {
        $shop = [ 'id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id'=> 4, 'title'=>'生活用品', 'shop_id'=>3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'cover_pic' => '/storage/pics/fans.jpg',
            'description' => '商品详情描述',
            'price' => 5432,
            'pictures' => 'pic_1.jpg, pic_2.jpg, pic_3.jpg',
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        DB::table('yac_products')->insert($pData);

        $skData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $seckill = Campaign::find($skData['id']);
        $seckill->addProduct($pData['id'], 400);
        $seckill->save();

        $result = Campaign::find($skData['id'])->getProducts();
        $this->assertEquals(1, $result->count());
        $this->assertEquals(400, $result->get(0)->pivot->campaign_price);
    }


    /**
     * @test
     */
    public function addParticipates()
    {
        $cData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($cData);

        $campaign = Campaign::find($cData['id']);
        $pData = [
            'products' => '3',
            'user_id' => 35,
            'order_id' => 12
        ];
        $campaign->addParticipate($pData);

        $this->assertDatabaseHas('yac_participates', $pData);
    }

    /**
     * @test
     */
    public function calculatePrice()
    {
        $cData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($cData);
        $ppData = [
            'strategy' => '\Orq\Laravel\YaCommerce\Domain\Campaign\Model\OverMinusPriceStrategy',
            'parameters' => json_encode([
                'order_total' => 300,
                'deduct_amount' => 20,
            ]),
            'campaign_id' => $cData['id'],
        ];
        DB::table('yac_price_policies')->insert($ppData);

        $order = $this->mock(Order::class, function ($mock) {
            $mock->shouldReceive('getTotal')->andReturn(300);
        });
        $campaign = Campaign::find($cData['id']);
        $this->assertEquals(280, $campaign->calculatePrice($order));
    }

    /**
     * @test
     */
    public function isQualified()
    {
        $cData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($cData);
        $qpData = [
            'strategy' => '\Orq\Laravel\YaCommerce\Domain\Campaign\Model\ParticipateCountQualificationStrategy',
            'parameters' => json_encode([
                'participate_limits' => 1
            ]),
            'campaign_id' => $cData['id'],
        ];
        DB::table('yac_qualification_policies')->insert($qpData);
        $userId = 309;
        $pData = [
            'campaign_id' => $cData['id'],
            'user_id' => $userId,
            'products' => '3,2',
        ];
        DB::table('yac_participates')->insert($pData);

        $user = $this->mock(UserInterface::class, function ($mock) use ($userId) {
            $mock->shouldReceive('getId')->andReturn($userId);
        });
        $order = $this->mock(Order::class, function ($mock) use ($user) {
            $mock->shouldReceive('getUser')->andReturn($user);
        });
        $campaign = Campaign::find($cData['id']);
        $this->assertFalse($campaign->isQualified($order));
    }
}
