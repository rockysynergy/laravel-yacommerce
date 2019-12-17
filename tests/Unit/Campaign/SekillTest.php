<?php

namespace Tests\YaCommerce\Unit\Campaign;

use Tests\MakeStringTrait;
use Orchestra\Testbench\TestCase;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill;

;

class SeckillTest extends TestCase
{
    /**
     * @test
     */
    public function getStartTime()
    {
        $seckill = new Seckill();
        $sTime = '2019-12-21 14:25:21';
        $seckill->start_time = $sTime;

        $this->assertEquals($sTime, $seckill->getStartTime()->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function getEndTime()
    {
        $seckill = new Seckill();
        $sTime = '2021-12-21 14:25:21';
        $seckill->end_time = $sTime;

        $this->assertEquals($sTime, $seckill->getEndTime()->format('Y-m-d H:i:s'));
    }


    /**
     * @test
     */
    public function isOver()
    {
        $seckill = new Seckill();
        $seckill->end_time = date('Y-m-d H:i:s', strtotime('-1 day'));
        $this->assertTrue($seckill->isOVer());
        $seckill->end_time = date('Y-m-d H:i:s', strtotime('+1 day'));
        $this->assertFalse($seckill->isOVer());
    }
}
