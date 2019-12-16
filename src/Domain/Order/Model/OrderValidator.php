<?php
namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\DddBase\DomainException;

class OrderValidator
{
    public function validate(Order $order):bool
    {
        if (!$order->getOrderNumber()) throw new DomainException('订单号不能为空！', 1564799852);
        if (!$order->getUserId()) throw new DomainException('所属用户的id不能为空！', 1564794557);

        return true;
    }
}
