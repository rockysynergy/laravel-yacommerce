<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Model;

use Illuminate\Support\Facades\Log;

class Variant extends AbstractProduct
{
    protected $table = 'yac_products';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected function makeRules():array
    {
        $rules = parent::makeRules();
        $rules['parent_id'] = 'required|gte:1';

        return $rules;
    }
}
