<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


interface PriceStrategyInterface
{
    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\PricePolicyInterface $pricePolicy
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInterface $order
     */
    public function calculate($policy, $order):int;
}
