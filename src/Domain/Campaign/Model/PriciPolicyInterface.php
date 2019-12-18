<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;

interface PricePolicyInterface
{
    /**
     * Calculate the price
     */
    public function calculatePrice(Order $order):int;
}
