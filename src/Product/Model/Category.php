<?php

namespace Orq\Laravel\YaCommerce\Product\Model;

use Orq\Laravel\YaCommerce\OrmModel;

class Category extends OrmModel
{

    protected $table = 'yac_categories';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Make validation rules for the model
     */
    protected function makeRules()
    {
        return [
            'title' => 'max:100',
            'pic' => 'max:120',
            'pid' => 'gte:0',
            'shopId' => 'gte:1',
        ];
    }

    
}
