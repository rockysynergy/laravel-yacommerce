<?php
namespace Orq\Laravel\YaCommerce\Shipment\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;

class ShipAddress extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'userId' => ['validId', [['请提供合法的用户id', 1565591638]]],
        'name' => ['maxStrLen:20', [['名字不能多于20个字符', 1565591676]]],
        'mobile' => ['minStrLen:11|maxStrLen:11', [['电话不能少于11个字符', 1565592467], ['电话不能多于11个字符', 1565591719]]],
        'address' => ['maxStrLen:255', [['收货地址不能多于255个字符', 1565591779]]],
        'isDefault' => ['inList:0,1', [['请提供合法的默认值', 1565591899]]],
        'tab' => ['maxStrLen:20', [['标签不能多于20个字符', 1565591934]]],
        'deleted' => ['inList:0,1', [['请提供合法的删除标志', 1565594200]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        $arr['user_id'] = $this->userId;
        $arr['name'] = $this->name;
        $arr['mobile'] = $this->mobile;
        $arr['address'] = $this->address;
        if ($this->isDefault) $arr['is_default'] = $this->isDefault;
        if ($this->tab) $arr['tab'] = $this->tab;

        return $arr;
    }


}
