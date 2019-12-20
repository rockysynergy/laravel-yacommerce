<?php

namespace Tests\YaCommerce\Functional\Campaign;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign;
use Orq\Laravel\YaCommerce\Domain\Campaign\Service\CampaignService;

class CampaignServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function createCampaignWithProduct()
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
            'title' => '年终大促',
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'products' => [
                [$pData['id'], 400]
            ]
        ];
        $campaignService = new CampaignService(resolve(Campaign::class));
        $campaignService->create($skData);

        $result = Campaign::find($skData['id']);
        $this->assertEquals($skData['title'], $result->title);

        $result = $result->getProducts();
        $this->assertEquals(1, $result->count());
        $this->assertEquals(400, $result->get(0)->pivot->campaign_price);
        $this->assertEquals($pData['title'], $result->get(0)->title);
    }

    /**
     * @test
     */
    public function createCampaignWithNoProduct()
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
            'title' => 'seckill',
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
        ];
        $campaignService = new CampaignService(resolve(Campaign::class));
        $campaignService->create($skData);

        $result = Campaign::find($skData['id']);
        $this->assertEquals('seckill', $result->title);

        $result = $result->getProducts();
        $this->assertEquals(0, $result->count());
    }

     /**
     * @test
     */
    public function createCampaignAddThePricePolicy()
    {
        $omParameters = [
            'order_total' => 400,
            'deduct_amound' => 40
        ];
        $cData = [
            'id' => 55,
            'title' => '年终大促，满300减100',
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'price_policy' => [
                'strategy' => 'over_minus',
                'parameters' => $omParameters
            ]
        ];
        $campaignService = new CampaignService(resolve(Campaign::class));
        $campaignService->create($cData);

        $omCampaign = Campaign::find($cData['id']);
        $this->assertEquals($cData['title'], $omCampaign->title);

        $result = $omCampaign->getProducts();
        $this->assertEquals(0, $result->count());
        $this->assertEquals($omParameters['order_total'], $omCampaign->pricePolicy->parameters['order_total']);
    }

     /**
     * @test
     */
    public function createCampaignAddTheQualificationPolicy()
    {
        $omParameters = [
            'participate_limits' => 400,
        ];
        $cData = [
            'id' => 55,
            'title' => '年终大促，满300减100',
            'start_time' => '2019-12-23 15:32:44',
            'end_time' => '2019-12-25 15:32:44',
            'qualification_policy' => [
                'strategy' => 'ParticipateCounts',
                'parameters' => $omParameters
            ]
        ];
        $campaignService = new CampaignService(resolve(Campaign::class));
        $campaignService->create($cData);

        $omCampaign = Campaign::find($cData['id']);
        $this->assertEquals($cData['title'], $omCampaign->title);

        $result = $omCampaign->getProducts();
        $this->assertEquals(0, $result->count());
        $this->assertEquals($omParameters['participate_limits'], $omCampaign->qualificationPolicy->parameters['participate_limits']);
    }


    /**
     * @test
     */
    public function updateCampaignDeleteProduct()
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
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign',
        ];
        $bCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pbData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign',
        ];
        DB::table('yac_campaign_product')->insert([$aCpData, $bCpData]);

        $campaignService = new CampaignService(resolve(Campaign::class));
        $bSkData = array_merge($skData, ['products' => [[$pData['id'], 30]]]);
        $campaignService->update($bSkData);

        $seckill = Campaign::find($skData['id']);
        $products = $seckill->getProducts();
        $this->assertEquals(1, $products->count());
        $this->assertEquals(30, $seckill->getProducts()->get(0)->pivot->campaign_price);
    }

    /**
     * @test
     */
    public function updateCampaignAddProduct()
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
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign',
            'campaign_price' => 40,
        ];
        $bCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pbData['id'],
        ];
        DB::table('yac_campaign_product')->insert([$aCpData]);

        $campaignService = new CampaignService(resolve(Campaign::class));
        $bSkData = array_merge($skData, [
            'products' => [
                [$pData['id'], 30],
                [$pbData['id'], 50],
            ]
        ]);
        $campaignService->update($bSkData);

        $seckill = Campaign::find($skData['id']);
        $products = $seckill->getProducts();
        $this->assertEquals(2, $products->count());
        $this->assertEquals(30, $seckill->getProducts()->get(0)->pivot->campaign_price);
        $this->assertEquals(50, $seckill->getProducts()->get(1)->pivot->campaign_price);
    }

    /**
     * @test
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
            'title' => '年终大促',
        ];
        DB::table('yac_campaigns')->insert($skData);
        $aCpData = [
            'campaign_id' => $skData['id'],
            'product_id' => $pData['id'],
            'campaign_type' => 'Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign',
        ];
        DB::table('yac_campaign_product')->insert([$aCpData]);

        $campaignService = new CampaignService(resolve(Campaign::class));
        $campaignService->delete($skData['id']);

        $this->assertEquals(0, DB::table('yac_campaign_product')->count());
    }

}
