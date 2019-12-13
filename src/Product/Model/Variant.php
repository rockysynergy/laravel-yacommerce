<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

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

    protected function makeRules()
    {
        $rules = parent::makeRules();
        $rules['pid'] = 'required|gte:1';
    }
}
