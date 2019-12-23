<?php

namespace Tests\YaCommerce\Functional\Campaign;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

class OrderTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function generateOrderNumberThrowsExceptionIfInvalidPrefixProvided()
    {
        $this->expectException(IllegalArgumentException::class);
        $this->expectExceptionCode(1564728410);
        $order = new Order();
        $order->generateOrderNumber('12');
    }

    /**
     * @test
     */
    public function generateTheDefaultValue()
    {
        $order = new Order();
        $this->assertEquals('1', $order->pay_status);
        $this->assertEquals('1', $order->pay_method);
    }
}
