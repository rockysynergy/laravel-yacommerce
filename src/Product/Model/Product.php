<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Illuminate\Support\Facades\Log;

class Product extends AbstractProduct
{
    protected $table = 'yac_products';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
