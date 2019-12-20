<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Illuminate\Support\Str;
use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\ParameterAttributeTrait;

class Participate extends OrmModel
{

    protected $table = 'yac_participates';

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'user_id' => 'required|numeric|gte:0',
            'campaign_id' => 'required|numeric|gte:0',
            'products' => 'required|max:100',
        ];
    }
}
