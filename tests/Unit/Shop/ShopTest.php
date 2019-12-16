<?php

namespace Tests\YaCommerce\Unit\Shop\Model;

use Orchestra\Testbench\TestCase;;
use Tests\MakeStringTrait;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\Shop\Model\Shop;

class ShopTest extends TestCase
{
    use MakeStringTrait;

    /**
     * @test
     */
    public function longNameThrowsException()
    {
        $this->expectExceptionCode(1565331805);
        $shop = new Shop();
        $shop->setName($this->makeStr(121));
        var_dump($shop->getName());
    }

    /**
     * @test
     */
    public function getPersistentData()
    {
        $data = ['id'=>2, 'name'=>'积分商城'];
        $shop = ModelFactory::make(Shop::class, $data);

        $this->assertEquals($data, $shop->getPersistData());
    }
}
