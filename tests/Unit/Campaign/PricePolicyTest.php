<?php

namespace Tests\YaCommerce\Unit\Campaign;

use Tests\MakeStringTrait;
use Orchestra\Testbench\TestCase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\PricePolicy;

class PricePolicyTest extends TestCase
{
    /**
     * @test
     */
    public function overMinusPolicyDeductAbsoluteAmount()
    {
        $pricePolicy = new PricePolicy();
        $pricePolicy->strategy = '\Orq\Laravel\YaCommerce\Domain\Campaign\Model\OverMinusPriceStrategy';
        $parameters = [
            'order_total' => 300,
            'deduct_amount' => 20
        ];
        $pricePolicy->parameters = $parameters;

        $order = $this->mock(Order::class, function ($mock) {
            $mock->shouldReceive('getTotal')->andReturn(300);
        });

        $result = $pricePolicy->calculatePrice($order);
        $this->assertEquals(280, $result);
    }

    /**
     * @test
     */
    public function overMinusPolicyDeductByDiscount()
    {
        $pricePolicy = new PricePolicy();
        $pricePolicy->strategy = '\Orq\Laravel\YaCommerce\Domain\Campaign\Model\OverMinusPriceStrategy';
        $parameters = [
            'order_total' => 300,
            'discount_rate' => 20
        ];
        $pricePolicy->parameters = $parameters;

        $order = $this->mock(Order::class, function ($mock) {
            $mock->shouldReceive('getTotal')->andReturn(300);
        });

        $result = $pricePolicy->calculatePrice($order);
        $this->assertEquals(240, $result);
    }
}
