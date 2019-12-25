<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;

interface PricePolicyInterface
{
    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     */
    public function calculatePrice($order):int;
}
