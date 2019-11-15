<?php

namespace Tests\YaCommerce\Unit\Shipment;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipTracking;

class ShipTrackingTest extends TestCase
{
    use MakeStringTrait;

    /**
     * @test
     */
    public function illegalShipAddressIdThrowsException()
    {
        $this->expectExceptionCode(1571638941);
        $sTracking = new ShipTracking();
        $sTracking->setShipaddressId(-1);
    }

    /**
     * @test
     */
    public function illegalOrderIdThrowsException()
    {
        $this->expectExceptionCode(1571301863);
        $sTracking = new ShipTracking();
        $sTracking->setOrderId(-1);
    }

    /**
     * @test
     */
    public function longShipnumberThrowsException()
    {
        $this->expectExceptionCode(1571301919);
        $sTracking = new ShipTracking();
        $sTracking->setShipnumber($this->makeStr(101));
    }

    /**
     * @test
     */
    public function longCarrierThrowsException()
    {
        $this->expectExceptionCode(1571302022);
        $sTracking = new ShipTracking();
        $sTracking->setCarrier($this->makeStr(51));
    }

    /**
     * @test
     */
    public function longTrackingThrowsException()
    {
        $this->expectExceptionCode(1571302359);
        $sTracking = new ShipTracking();
        $sTracking->setTracking($this->makeStr(5001));
    }

    /**
     * @test
     */
    public function illegalTrackingStatusThrowsException()
    {
        $this->expectExceptionCode(1571302117);
        $sTracking = new ShipTracking();
        $sTracking->setTrackingStatus(-1);
    }

    /**
     * @test
     */
    public function longUpdatedAtThrowsException()
    {
        $this->expectExceptionCode(1571302468);
        $sTracking = new ShipTracking();
        $sTracking->setUpdatedAt($this->makeStr(51));
    }

    /**
     * @test
     */
    public function getPersistData()
    {
        $data = [
            'order_id' => 32,
            'shipnumber' => 'ST2019071233',
            'carrier' => '圆通快递',
            'tracking_status' => '0',
            'tracking' => '[{
                "context":"上海分拨中心/装件入车扫描 ",
                "time":"2012-08-28 16:33:19",
                "ftime":"2012-08-28 16:33:19",
                "status":"在途",
                "areaCode":"310000000000",
                "areaName":"上海市"
            }]',
            'updated_at' => '2019-10-12 14:23:34',
            'shipaddress_id' => 98,
        ];
        $ShipTracking = ModelFactory::make(ShipTracking::class, $data);
        $this->assertEquals($data, $ShipTracking->getPersistData());
    }
}
