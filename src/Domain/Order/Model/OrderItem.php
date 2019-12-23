<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;


class OrderItem  extends OrmModel
{
    protected $table = 'yac_orderitems';

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'order_id' => 'gte:0',
            'thumbnail' => 'max:200',
            'title' => 'max:200',
            'info' => 'max:200',
            'amount' => 'required|gte:1',
            'unit_price' => 'numeric|gte:0',
            'pay_amount' => 'numeric|gte:0',
        ];
    }
}
