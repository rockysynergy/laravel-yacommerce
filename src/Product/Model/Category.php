<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;

class Category extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'title' => ['maxStrLen:100', [['类别不能多于100个字符', 1565331955]]],
        'pic' => ['maxStrLen:120', [['类别图片地址不能多于120个字符', 1565332035]]],
        'parentId' => ['validId', [['请提供合法的类别id', 1565332088]]],
        'shopId' => ['validId', [['请提供合法的商铺id', 1565332125]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        $arr['title'] = $this->title;
        $arr['shop_id'] = $this->shopId;
        if ($this->id) $arr['id'] = $this->id;
        if ($this->pic) $arr['pic'] = $this->pic;
        if ($this->parentId) $arr['parent_id'] = $this->parentId;

        return $arr;
    }

}
