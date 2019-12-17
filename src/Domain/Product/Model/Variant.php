<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Model;

use Illuminate\Support\Facades\Log;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

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

    /**
     * find instance by id.
     * Merge pictures, description and parameters with parent product
     *
     * @param int $id
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @return Orq\Laravel\YaCommerce\Domain\Product\Model\ormModel
     */
    public function findById(int $id)
    {
        $model = self::find($id);
        if (!$model) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576480181);
        $parent = Self::find($model->parent_id);
        if ($parent) {
            $model->pictures = "{$model->pictures}， {$parent->pictures}";
            $model->description = "{$model->description}， {$parent->description}";
            $model->parameters = array_merge($parent->parameters, $model->parameters);
        }
        return $model;
    }
}
