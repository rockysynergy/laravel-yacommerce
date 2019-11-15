<?php
namespace Orq\Laravel\YaCommerce\Shipment\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;

class ShipTracking extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'orderId' => ['validId', [['请提供合法的订单id', 1571301863]]],
        'shipnumber' => ['maxStrLen:100', [['运单号不能多于100个字符', 1571301919]]],
        'carrier' => ['maxStrLen:50', [['快递公司名称不能多于50个字符', 1571302022]]],
        'trackingStatus' => ['positiveNumber', [['跟踪状态不能为负数', 1571302117]]],
        'tracking' => ['maxStrLen:5000', [['跟踪状态不能多于5000个字符', 1571302359]]],
        'updatedAt' => ['maxStrLen:50', [['时间不能多于50个字符', 1571302468]]],
        'shipaddressId' => ['validId', [['请提供合法的地址记录ID', 1571638941]]],
    ];

    public function getPersistData():array
    {
        $arr = [];
        $arr['order_id'] = $this->orderId;
        $arr['shipnumber'] = $this->shipnumber;
        $arr['carrier'] = $this->carrier;
        if ($this->updatedAt) $arr['updated_at'] = $this->updatedAt;
        if ($this->tracking) $arr['tracking'] = $this->tracking;
        if (isset($this->trackingStatus)) $arr['tracking_status'] = $this->trackingStatus;
        if (isset($this->shipaddressId)) $arr['shipaddress_id'] = $this->shipaddressId;

        return $arr;
    }


}
