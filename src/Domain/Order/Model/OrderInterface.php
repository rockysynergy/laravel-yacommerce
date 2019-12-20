<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

interface OrderInterface
{

    /**
     * @return Orq\Laravel\YaCommerce\Domain\UserInterface
     */
    public function getUser();
}
