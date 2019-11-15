<?php

namespace Orq\Laravel\YaCommerce\AppServices\Admin;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Product\Model\ProductVariant;
use Orq\Laravel\YaCommerce\Product\Repository\ProductVariantRepository;

class YacProductVariantService
{

    public static function saveNew(array $data):void
    {
        $data['price'] *= 100;
        $data['show_price'] *= 100;

        $prod = ModelFactory::make(ProductVariant::class, $data);
        ProductVariantRepository::save($prod);
    }

    public static function updateItem(array $data):void
    {
        $data['price'] *= 100;
        $data['show_price'] *= 100;

        $prod = ModelFactory::make(ProductVariant::class, $data);
        ProductVariantRepository::update($prod);
    }
}
