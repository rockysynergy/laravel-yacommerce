<?php

namespace Tests\YaCommerce\Unit\Cart;

use Tests\MakeStringTrait;

use Orq\DddBase\ModelFactory;
use Orchestra\Testbench\TestCase;;
use Orq\Laravel\YaCommerce\Domain\Order\Model\CartItem;

class CartItemTest extends TestCase
{
    use MakeStringTrait;

    /**
     * @test
    */
    public function illegalProductIdThrowsException()
    {
        $this->expectExceptionCode(1566026059);
        $item = new CartItem();
        $item->setProductId(-1);
    }

    /**
     * @test
    */
    public function illegalUserIdThrowsException()
    {
        $this->expectExceptionCode(1566026095);
        $item = new CartItem();
        $item->setUserId(-1);
    }

    /**
     * @test
    */
    public function illegalShopIdThrowsException()
    {
        $this->expectExceptionCode(1566182617);
        $item = new CartItem();
        $item->setShopId(-1);
    }

    /**
     * @test
    */
    public function negativeAmountThrowsException()
    {
        $this->expectExceptionCode(1566026110);
        $item = new CartItem();
        $item->setAmount(-1);
    }

    /**
     * @test
    */
    public function shortCreatedAtThrowsException()
    {
        $this->expectExceptionCode(1566026242);
        $item = new CartItem();
        $item->setCreatedAt('2019:12:23 03:45:4');
    }

    /**
     * @test
    */
    public function longCreatedAtThrowsException()
    {
        $this->expectExceptionCode(1566026263);
        $item = new CartItem();
        $item->setCreatedAt('2019:12:23 03:45:444');
    }

    /**
     * @test
     */
    public function persistData()
    {
        $data = [
            'product_id'=>3,
            'user_id'=>5,
            'shop_id'=>7,
            'amount'=>3,
            'created_at'=>'2019:03:23 14:33:45',
        ];

        $cartItem = ModelFactory::make(CartItem::class, $data);
        $this->assertEquals($data, $cartItem->getPersistData());
    }
}
