<?php

namespace Tests\YaCommerce\Order\Functional\Repository;


use Tests\DbTestCase;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Payment\WxPay;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WxPayTest  extends DbTestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function makeUnifiedOrder()
    {
        $this->markTestSkipped('revist this after refactored WxPay.');
        $info = [
            'body'=>'会费',
            'out_trade_no'=>'HF201908031123423952',
            'total_fee'=>10,
            'openid'=>'oebgg5V-gQIeH4bjxHFsVI3i2Gv8',
            'notify_url' => 'http://my-domain.com/pay_notify',
        ];

        $result = WxPay::makeUnifiedOrder($info);
        $this->assertRegExp('/^wx.*/', $result);
    }
}
