<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

interface OrderServiceInterface
{

    /**
     * @return Orq\Laravel\YaCommerce\Domain\UserInterface
     */
    public function getUser();

    /**
     * @param array $info
     * @return void
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function makeOrder(array $info):void;
}
