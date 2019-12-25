<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Model;

use Illuminate\Support\Facades\Log;

class Product extends AbstractProduct
{
    protected $table = 'yac_products';
    protected $model = 'product';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
