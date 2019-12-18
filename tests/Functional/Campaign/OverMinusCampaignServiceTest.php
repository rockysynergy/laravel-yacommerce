<?php

namespace Tests\YaCommerce\Functional\Campaign;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\OverMinusCampaign;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill;
use Orq\Laravel\YaCommerce\Domain\Campaign\Service\OverMinusCampaignService;
use Orq\Laravel\YaCommerce\Domain\Campaign\Service\SeckillService;

class OverMinusCampaignServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function createOverMinusCampaignAddThePricePolicy()
    {
        $omParameters = [
            'order_total' => 400,
            'deduct_amound' => 40
        ];
        $skData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'price_policy' => [
                'type' => 'over_minus',
                'parameters' => $omParameters
            ]
        ];
        $omCampaignService = new OverMinusCampaignService(resolve(OverMinusCampaign::class));
        $omCampaignService->create($skData);

        $omCampaign = OverMinusCampaign::find($skData['id']);
        $this->assertEquals('overMinus', $omCampaign->campaign_type);

        $result = $omCampaign->getProducts();
        $this->assertEquals(0, $result->count());
        $this->assertEquals($omParameters['order_total'], $omCampaign->pricePolicy->parameters['order_total']);
    }

    /**
     */
    public function createSeckillWithNoProduct()
    {
        $shop = ['id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id' => 4, 'title' => '生活用品', 'shop_id' => 3];
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
        $omCampaignService = new SeckillService(resolve(Seckill::class));
        $omCampaignService->create($skData);

        $result = Seckill::find($skData['id']);
        $this->assertEquals('overMinus', $result->campaign_type);

        $result = $result->getProducts();
        $this->assertEquals(0, $result->count());
    }

    /**
     */
    public function updateSeckillDeleteProduct()
    {
        $shop = ['id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id' => 4, 'title' => '生活用品', 'shop_id' => 3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'price' => 5432,
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        $pbData = [
            'id' => 25,
            'title' => '电风z扇',
            'price' => 5222,
            'category_id' => $category['id'],
            'inventory' => 10,
            'status' => 1,
        ];
        DB::table('yac_products')->insert([$pData, $pbData]);
        $skData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'campaign_type' => 'seckill',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill',
        ];
        $bCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pbData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill',
        ];
        DB::table('yac_campaign_product')->insert([$aCpData, $bCpData]);

        $omCampaignService = new SeckillService(resolve(Seckill::class));
        $bSkData = array_merge($skData, ['products' => [[$pData['id'], 30]]]);
        $omCampaignService->update($bSkData);

        $seckill = Seckill::find($skData['id']);
        $products = $seckill->getProducts();
        $this->assertEquals(1, $products->count());
        $this->assertEquals(30, $seckill->getProducts()->get(0)->pivot->campaign_price);
    }

    /**
     */
    public function updateSeckillAddProduct()
    {
        $shop = ['id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id' => 4, 'title' => '生活用品', 'shop_id' => 3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'price' => 5432,
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        $pbData = [
            'id' => 25,
            'title' => '电风z扇',
            'price' => 5222,
            'category_id' => $category['id'],
            'inventory' => 10,
            'status' => 1,
        ];
        DB::table('yac_products')->insert([$pData, $pbData]);
        $skData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'campaign_type' => 'seckill',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill',
            'campaign_price' => 40,
        ];
        $bCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pbData['id'],
        ];
        DB::table('yac_campaign_product')->insert([$aCpData]);

        $omCampaignService = new SeckillService(resolve(Seckill::class));
        $bSkData = array_merge($skData, [
            'products' => [
                [$pData['id'], 30],
                [$pbData['id'], 50],
            ]
        ]);
        $omCampaignService->update($bSkData);

        $seckill = Seckill::find($skData['id']);
        $products = $seckill->getProducts();
        $this->assertEquals(2, $products->count());
        $this->assertEquals(30, $seckill->getProducts()->get(0)->pivot->campaign_price);
        $this->assertEquals(50, $seckill->getProducts()->get(1)->pivot->campaign_price);
    }

    /**
     */
    public function removesPivotTableRecordsUponDeletion()
    {
        $shop = ['id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id' => 4, 'title' => '生活用品', 'shop_id' => 3];
        DB::table('yac_categories')->insert($category);
        $pData = [
            'id' => 2,
            'title' => '电风扇',
            'price' => 5432,
            'category_id' => $category['id'],
            'inventory' => 0,
            'status' => 1,
        ];
        $pbData = [
            'id' => 25,
            'title' => '电风z扇',
            'price' => 5222,
            'category_id' => $category['id'],
            'inventory' => 10,
            'status' => 1,
        ];
        DB::table('yac_products')->insert([$pData, $pbData]);
        $skData = [
            'id' => 55,
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'campaign_type' => 'seckill',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill',
        ];
        DB::table('yac_campaign_product')->insert([$aCpData]);

        $omCampaignService = new SeckillService(resolve(Seckill::class));
        $omCampaignService->delete($skData['id']);

        $this->assertEquals(0, DB::table('yac_campaign_product')->count());
    }
}
