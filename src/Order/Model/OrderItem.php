<?php
namespace Orq\Laravel\YaCommerce\Order\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;

class OrderItem extends AbstractEntity
{

    use IdTrait;

    protected $fieldsConf = [
        'orderId' => ['validId', [['订单id不能小于0！', 1564735291]]],
        'thumbnail' => ['maxStrLen:200', [['缩略图地址不能多于200个字符', 1564735346]]],
        'title' => ['maxStrLen:200', [['标题不能多于200个字符', 1564735384]]],
        'info' => ['maxStrLen:200', [['额外信息不能多于200个字符', 1564735423]]],
        'amount' => ['positiveNumber', [['数量不能小于0', 1564735559]]],
        'unitPrice' => ['isNumeric', [['单价必须是数字', 1564735709]]],
        'payAmount' => ['isNumeric', [['支付金额必须是数字', 1564735762]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        if ($this->id) $arr['id'] = $this->id;
        if ($this->info) $arr['info'] = $this->info;
        $arr['order_id'] = $this->orderId;
        $arr['thumbnail'] = $this->thumbnail;
        $arr['title'] = $this->title;
        $arr['amount'] = $this->amount;
        $arr['unit_price'] = $this->unitPrice;
        $arr['pay_amount'] = $this->payAmount;

        return $arr;
    }
}
