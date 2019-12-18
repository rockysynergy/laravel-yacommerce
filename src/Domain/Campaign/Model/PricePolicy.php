<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Illuminate\Support\Str;
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
            'type' => 'required|max:50',
            'parameters' => 'required|max:500',
        ];
    }

    /**
     * Calculate the price
     */
    public function calculatePrice(Order $order):int
    {
        $priceStratege = Str::camel($this->type) . 'Strategy';
        return $this->$priceStratege($order);
    }

    protected function overMinusStrategy(Order $order):int
    {
        $orderTotal = $order->getTotal();
        if ($orderTotal >= $this->parameters['order_total']) {
            return $orderTotal - $this->parameters['deduct_amount'];
        } else {
            return $orderTotal;
        }
    }
}
