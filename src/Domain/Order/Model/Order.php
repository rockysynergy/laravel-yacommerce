<?php
namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\KvMapVo;
use Orq\DddBase\AbstractEntity;
use Orq\DddBase\TimeStampTrait;
use Orq\DddBase\IllegalArgumentException;
use Orq\DddBase\DomainException;

class Order extends AbstractEntity
{

    use IdTrait;
    use TimeStampTrait;
    protected $items;

    protected $fieldsConf = [
        'orderNumber' => ['minStrLen:20|maxStrLen:20', [['订单号不能少于21个字符！', 1564725800], ['订单号不能多于22个字符！', 1564725814]]],
        'exorderNumber' => ['maxStrLen:30', [['外部订单号不能多于30个字符', 1564725821]]],
        'payAmount' => ['isNumeric', [['支付金额必须是数字', 1564732892]]],
        'userId' => ['validId', [['用户id不合法', 1564793861]]],
        'shipaddressId' => ['validId', [['收货地址id不合法', 1565596966]]],
        'deleted' => ['inList:0,1', [['请提供合法的删除标志', 1565596989]]],
        'ptype' => ['maxStrLen:15', [['所属实体类型名字不能超过15个字符', 1565851605]]],
        'pid' => ['validId', [['请提供合法的所属实体类型id', 1565851783]]],
        'shiptrackingId' => ['validId', [['请提供合法的物流跟踪记录号', 1571296443]]],
    ];

    public function __construct(string $orderNoPrefix=NULL)
    {
        parent::__construct();

        if (!is_null($orderNoPrefix)) {
            // Make from record in database does not need to generate order number
            $this->generateOrderNumber($orderNoPrefix);
        }
        $this->payStatus = new KvMapVo('pay_status', 1);
        $this->payMethod = new KvMapVo('pay_method', 1);
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    protected function generateOrderNumber(string $prefix)
    {
        $orderNoPrefix = strtoupper($prefix);
        if (preg_match('/[A-Z][A-Z]/', $orderNoPrefix) !== 1) {
            throw new DomainException('订单号前缀必须是2个英文字符！', 1564728410);
        }
        $this->orderNumber = $orderNoPrefix.date('YmdHis').rand(1000,9999);
    }

    /**
     * 设置模块
     */
    public function setPayStatus($status):void
    {
        $status = (int) $status;
        if ($status < 1 || $status > 3) {
            throw new IllegalArgumentException('支付状态不正确！', 1564725830);
        }
        $this->payStatus = new KvMapVo('pay_status', $status);
    }

    public function getPayStatus():KvMapVo
    {
        return $this->payStatus;
    }

    /**
     * 设置模块
     */
    public function setPayMethod($method):void
    {
        $method = (int) $method;
        if ($method < 1 || $method > 5) {
            throw new IllegalArgumentException('支付方式不正确！', 1564726085);
        }
        $this->payMethod = new KvMapVo('pay_method', $method);
    }

    public function getPayMethod():KvMapVo
    {
        return $this->payMethod;
    }

    public function setItems(array $items):void
    {
        $this->items = $items;
    }

    public function getPersistData():array
    {
        $arr = [];
        if ($this->id) $arr['id'] = $this->id;
        if ($this->payStatus) $arr['pay_status'] = $this->getPayStatus()->getKey();
        if ($this->createdAt) $arr['created_at'] = $this->getCreatedAt('Y-m-d H:i:s');
        if ($this->updatedAt) $arr['updated_at'] = $this->getUpdatedAt('Y-m-d H:i:s');
        $arr['order_number'] = $this->orderNumber;
        $arr['pay_method'] = $this->getPayMethod()->getKey();
        $arr['user_id'] = $this->userId;

        $arr['pid'] = $this->pid;
        $arr['ptype'] = $this->ptype;
        if ($this->exorderNumber) $arr['exorder_number'] = $this->exorderNumber;
        if ($this->payAmount) $arr['pay_amount'] = $this->payAmount;
        if ($this->shipaddressId) $arr['shipaddress_id'] = $this->shipaddressId;
        $arr['deleted'] = $this->deleted ?? 0;
        if ($this->shiptrackingId) $arr['shiptracking_id'] = $this->shiptrackingId;

        return $arr;
    }

    /**
     * derive order information from orderItems
     */
    public function getDescription():string
    {
        if (count($this->items) == 0) { return ''; }
        $d = $this->items[0]->getTitle();
        if (count($this->items)>1) { $d .= '等';}

        return $d;
    }
}
