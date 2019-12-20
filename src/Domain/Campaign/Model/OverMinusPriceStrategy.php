<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;


/**
 * OverMinus Policy
 *
 * It requires `order_total` and `deduct_amount`
 */
class OverMinusPriceStrategy implements PriceStrategyInterface
{

    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\PricePolicyInterface $pricePolicy
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\Order $order
     * @return int
     */
    public function calculate($policy, $order):int
    {
        $orderTotal = $order->getTotal();
        if ($orderTotal >= $policy->parameters['order_total']) {
            if (isset($policy->parameters['deduct_amount'])) $orderTotal -= $policy->parameters['deduct_amount'];
            if (isset($policy->parameters['discount_rate'])) $orderTotal -= $orderTotal * $policy->parameters['discount_rate'] / 100;
        }
        return $orderTotal;
    }
}
