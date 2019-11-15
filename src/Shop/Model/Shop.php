<?php
namespace Orq\Laravel\YaCommerce\Shop\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;

class Shop extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'name' => ['maxStrLen:120', [['商铺名称不能多于120个字符', 1565331805]]],
        'type' => ['maxStrLen:100', [['商铺名称不能多于100个字符', 1567664438]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        if ($this->id) $arr['id'] = $this->id;
        $arr['name'] = $this->name;
        if ($this->type) $arr['type'] = $this->type;

        return $arr;
    }

}
