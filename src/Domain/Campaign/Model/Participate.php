<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participate extends OrmModel
{

    use SoftDeletes;
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
