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
    public function overMinusStrategy()
    {
        $pricePolicy = new PricePolicy();
        $pricePolicy->type = 'over_minus';
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
}
