<?php

namespace Orq\Laravel\YaCommerce\Product\Model;

use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\OrmModel;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

abstract class AbstractProduct extends OrmModel
{

    /**
     * Make validation rules for the model
     */
    protected function makeRules()
    {
        return [
            'title' => 'required|max:100',
            'coverPic' => 'required|max:300',
            'description' => 'max:20000',
            'price' => 'gte:0',
            'showPrice' => 'gte:0',
            'pictures' => 'max:500',
            'categoryId' => 'gte:0',
            'inventory' => 'gte:0',
            'status' => 'in:0,1',
            'parameters' => 'max:3000',
        ];
    }

    /**
     * parameter Accessor
     */
    public function getParameterAttribute($value): array
    {
        return json_decode($value, true);
    }

    /**
     * parameter Mutator
     */
    public function setParameterAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['parameter'] = json_encode($value);
        } else if (is_string($value)) {
            $this->attributes['parameter'] = $value;
        }
    }

    public function decInventory(int $num): void
    {
        if ($num < 1) {
            throw new IllegalArgumentException(trans('YaCommerce:message.inventory_not-positive'), 1576138822);
        }
        if ($this->inventory < $num) {
            throw new IllegalArgumentException(trans('YaCommerce:message.inventory_not-enough'), 1565744840);
        }

        $this->decrement('inventory', $num);
    }

    public function incInventory(int $num): void
    {
        if ($num < 1) {
            throw new IllegalArgumentException(trans('YaCommerce:message.inventory_not-positive'), 1576138822);
        }

        $this->decrement('inventory', $num);
    }
}
