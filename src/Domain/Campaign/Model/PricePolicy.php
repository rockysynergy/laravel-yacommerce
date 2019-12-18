<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\ParameterAttributeTrait;

class PricePolicy extends OrmModel
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

}
