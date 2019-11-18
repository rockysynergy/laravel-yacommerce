<?php

namespace Tests\YaCommerce\Functional\Shipment;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Shipment\Service\ShipAddressService;
use Orq\Laravel\YaCommerce\UserInterface;

class ShipAddressServiceTest  extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function userHasAddress()
    {
        $address = [ 'user_id' => 3,'name' => '潘耶林', 'mobile'=>'13987166813', 'address'=>'广东省兴宁市广北3路'];
        DB::table('yac_shipaddresses')->insert($address);
        $re = DB::table('yac_shipaddresses')->first();

        $this->assertFalse(ShipAddressService::userHasAddress(1, $re->id));
        $this->assertTrue(ShipAddressService::userHasAddress(3, $re->id));
    }

    /**
     * @test
     */
    public function getAllForUser()
    {
        $userId = 3;
        $address_1 = [ 'user_id' =>$userId,'name' => '潘耶林', 'mobile'=>'13987166813', 'address'=>'广东省兴宁市广北3路'];
        $address_2 = [ 'user_id' =>$userId+1 ,'name' => '不知道', 'mobile'=>'13987166813', 'address'=>'广东省兴宁市广北3路'];
        DB::table('yac_shipaddresses')->insert([$address_1, $address_2]);
        $wqbUser = new User();
        $wqbUser->setId($userId);
        $re = ShipAddressService::getAllForUser($wqbUser);

        $this->assertEquals(1, count($re));
        $this->assertEquals($address_1['name'], $re[0]['name']);
    }
}

class User implements UserInterface
{
    protected $id;

    public function setId($userId)
    {
        $this->id= $userId;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
