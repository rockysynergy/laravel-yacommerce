<?php

namespace Tests\YaCommerce\Functional\Campaign;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill;
use Orq\Laravel\YaCommerce\Domain\Campaign\Service\SeckillService;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

class SeckillTest  extends DbTestCase
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
        ];
        DB::table('yac_seckills')->insert($skData);
        $seckill = Seckill::find($skData['id']);
        $seckill->addProduct($pData['id'], 400);
        $seckill->save();

        $result = Seckill::find($skData['id'])->getProducts();
        $this->assertEquals(1, $result->count());
        $this->assertEquals(400, $result->get(0)->pivot->campaign_price);
    }

}
