<?php
namespace Orq\Laravel\YaCommerce\Shipment\Model;;

use Orq\DddBase\DomainException;

class ShipAddressValidator
{
    public function validate(ShipAddress $shipaddress):bool
    {
        if (!$shipaddress->getName()) throw new DomainException('请填写收货人！', 1565924375);
        if (!$shipaddress->getMobile()) throw new DomainException('请填写联系电话！', 1565924442);
        if (!$shipaddress->getAddress()) throw new DomainException('请填写地址', 1565924466);
        return true;
    }
}
