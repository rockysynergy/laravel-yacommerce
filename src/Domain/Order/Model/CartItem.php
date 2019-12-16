<?php
namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;
use Orq\DddBase\DomainException;

class CartItem extends AbstractEntity
{
    use IdTrait;


    protected $fieldsConf = [
        'productId' => ['validId', [['产品id不能小于0！', 1566026059]]],
        'userId' => ['validId', [['用户id不能小于0！', 1566026095]]],
        'shopId' => ['validId', [['商店id不能小于0！', 1566182617]]],
        'amount' => ['positiveNumber', [['数量不能小于0', 1566026110]]],
        'createdAt' => ['minStrlen:19|maxStrlen:19', [['不能少于19个字符', 1566026242], ['不能多于19个字符', 1566026263]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        if ($this->id) $arr['id'] = $this->id;
        if ($this->createdAt) $arr['created_at'] = $this->createdAt;
        $arr['product_id'] = $this->productId;
        $arr['user_id'] = $this->userId;
        $arr['amount'] = $this->amount;
        $arr['shop_id'] = $this->shopId;

        return $arr;
    }


    public function getAddTime(string $format=null)
    {
        $n = new \DateTime($this->createdAt);
        if (is_null($format)) return $n;

        return $n->format($format);
    }
}
