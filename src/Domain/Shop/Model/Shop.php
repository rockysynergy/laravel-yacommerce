<?php
namespace Orq\Laravel\YaCommerce\Domain\Shop\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;

class Shop extends OrmModel
{
    protected $table = 'yac_shops';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected function makeRules():array
    {
        return [
            'name' => 'max:120',
            'type' => 'max:100',
        ];
    }

}
