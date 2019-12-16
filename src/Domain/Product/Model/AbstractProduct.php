<?php

namespace Orq\Laravel\YaCommerce\Domain\Product\Model;

use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

abstract class AbstractProduct extends OrmModel
{

    /**
     * Make validation rules for the model
     */
    protected function makeRules():array
    {
        return [
            'title' => 'required|max:100',
            'cover_pic' => 'required|max:300',
            'description' => 'required|max:20000',
            'price' => 'required|gte:0',
            'show_price' => 'gte:0',
            'pictures' => 'max:500',
            'category_id' => 'required|gte:0',
            'inventory' => 'required|gte:0',
            'status' => 'required|in:0,1',
            'parameters' => 'max:3000',
            'model' => 'max:300',
            'variants' => 'max:70',
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
