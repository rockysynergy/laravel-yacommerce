<?php
namespace Orq\Laravel\YaCommerce\Shop\Model;

use Orq\Laravel\YaCommerce\OrmModel;

class Shop extends OrmModel
{
    protected $table = 'yac_shops';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected function getRules() {
        return [
            'name' => 'max:120',
            'type' => 'max:100',
        ];
    }

}
