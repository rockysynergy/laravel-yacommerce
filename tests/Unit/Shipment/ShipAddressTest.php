<?php

namespace Tests\YaCommerce\Unit\Shipment;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Shipment\Model\ShipAddress;

class ShipAddressTest extends TestCase
{
    use MakeStringTrait;


    /**
     * @test
     */
    public function illegalUserIdThrowsException()
    {
        $this->expectExceptionCode(1565591638);
        $saddress = new ShipAddress();
        $saddress->setUserId(-1);
    }

    /**
     * @test
     */
    public function longNameThrowsException()
    {
        $this->expectExceptionCode(1565591676);
        $saddress = new ShipAddress();
        $saddress->setName($this->makeStr(21));
    }

    /**
     * @test
     */
    public function shortMobileThrowsException()
    {
        $this->expectExceptionCode(1565592467);
        $saddress = new ShipAddress();
        $saddress->setMobile($this->makeStr(10));
    }

    /**
     * @test
     */
    public function longMobileThrowsException()
    {
        $this->expectExceptionCode(1565591719);
        $saddress = new ShipAddress();
        $saddress->setMobile($this->makeStr(12));
    }

    /**
     * @test
     */
    public function longAddressThrowsException()
    {
        $this->expectExceptionCode(1565591779);
        $saddress = new ShipAddress();
        $saddress->setAddress($this->makeStr(256));
    }

    /**
     * @test
     */
    public function illegalIsDefaultThrowsException()
    {
        $this->expectExceptionCode(1565591899);
        $saddress = new ShipAddress();
        $saddress->setIsDefault(2);
    }

    /**
     * @test
     */
    public function illegalDeletedThrowsException()
    {
        $this->expectExceptionCode(1565594200);
        $saddress = new ShipAddress();
        $saddress->setDeleted(2);
    }

    /**
     * @test
     */
    public function longTabThrowsException()
    {
        $this->expectExceptionCode(1565591934);
        $saddress = new ShipAddress();
        $saddress->setTab($this->makeStr(21));
    }

    /**
     * @test
     */
    public function getPersistData()
    {
        $data = [
            'user_id'=>3,
            'name'=>'潘耶林',
            'mobile'=>'13876778139',
            'address'=>'广东省佛山市禅城区ji'
        ];
        $shipAddress = ModelFactory::make(ShipAddress::class, $data);
        $this->assertEquals($data, $shipAddress->getPersistData());
    }
}
