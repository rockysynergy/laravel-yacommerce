<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\ParameterAttributeTrait;

class PricePolicy extends OrmModel implements PricePolicyInterface
{
    use ParameterAttributeTrait;

    protected $table = 'yac_price_policies';

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'strategy' => 'required|max:150',
            'parameters' => 'required|max:500',
        ];
    }

    /**
     * Calculate the price.
     */
    public function calculatePrice($order):int
    {
        $strategy = resolve($this->strategy);
        return $strategy->calculate($this, $order);
    }
}