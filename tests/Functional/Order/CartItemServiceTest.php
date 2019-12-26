<?php

namespace Tests\YaCommerce\Functional\Order;


use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\CartItem;
use Orq\Laravel\YaCommerce\Domain\Order\Service\CartItemService;

class CartItemServiceTest extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function addItem()
    {
        $data = [
            'product_id' => 3,
            'amount' => 5,
            'shop_id' => 9,
            'user_id' => 900,
        ];
        $cartItemService = new CartItemService(new CartItem());
        $cartItemService->addItem($data);

        $this->assertDatabaseHas('yac_cartitems', $data);
    }

    /**
     * @test
     */
    public function getCartItems()
    {
        $shop = ['id' => 3, 'name' => '积分商城'];
        DB::table('yac_shops')->insert($shop);
        $category = ['id' => 4, 'title' => '生活用品', 'shop_id' => 3];
        DB::table('yac_categories')->insert($category);
        $product_a = [
            'id' => 5,
            'title' => 'product a',
            'cover_pic' => 'product_a_pic.jpg',
            'price' => '33.44',
            'description' => 'product a is the best of its kind',
            'status' => 1,
            'inventory' => 13,
            'category_id' => 4,
        ];
        DB::table('yac_products')->insert($product_a);

        $cItem = [
            'product_id' => 5,
            'amount' => 4,
            'user_id' => 3,
            'shop_id' => 8
        ];
        DB::table('yac_cartitems')->insert($cItem);

        $cartItemService = new CartItemService(new CartItem());
        $re = $cartItemService->getAllForUser(['user_id' => 3, 'shop_id' => 8]);
        $this->assertEquals($product_a['title'], $re->get(0)->product->title);
    }
}
