<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Orq\DddBase\AbstractEntity;
use Orq\DddBase\IdTrait;

class SeckillProduct extends AbstractEntity
{
    use IdTrait;


    protected $fieldsConf = [
        'id' => ['validId', [['请提供合法的id', 1565332392]]],
        'title' => ['maxStrLen:100', [['商品名称不能多于100个字符', 1565332430]]],
        'coverPic' => ['maxStrLen:300', [['头图不能多于300个字符', 1565332481]]],
        'description' => ['maxStrLen:2000', [['详情不能多于2000个字符', 1565332518]]],
        'pictures' => ['maxStrLen:500', [['图片不能多于500个字符', 1565332604]]],
        'price' => ['positiveNumber', [['请提供合法的秒杀价格', 1566283361]]],
        'categoryId' => ['validId', [['请提供合法的类别id', 1565332642]]],

        'inventory' => ['positiveNumber', [['请提供合法的库存', 1565744941]]],
        'status' => ['inList:0,1', [['请提供合法的状态', 1565840760]]],
        'startTime' => ['minStrlen:19|maxStrlen:19', [['开始时间不能少于19个字符', 1566282824], ['开始时间不能多于19个字符', 1566282832]]],
        'endTime' => ['minStrlen:19|maxStrlen:19', [['结束时间不能少于19个字符', 1566282839], ['结束时间不能多于19个字符', 1566282846]]],
        'total' => ['positiveNumber', [['请提供合法的库存', 1566282894]]],
        'skPrice' => ['positiveNumber', [['请提供合法的秒杀价格', 1566282926]]],

    ];

    public function getPersistData():array
    {
        $arr = [];
        $arr['title'] = $this->title;
        $arr['category_id'] = $this->categoryId;
        if (isset($this->status)) $arr['status'] = $this->status;
        if ($this->coverPic) $arr['cover_pic'] = $this->coverPic;
        if ($this->description) $arr['description'] = $this->description;
        if (isset($this->price)) $arr['price'] = $this->price;
        if ($this->pictures) $arr['pictures'] = $this->pictures;
        if ($this->id) $arr['id'] = $this->id;
        if (!is_null($this->inventory)) $arr['inventory'] = $this->inventory;
        if ($this->startTime) $arr['start_time'] = $this->startTime;
        if ($this->endTime) $arr['end_time'] = $this->endTime;
        if ($this->total) $arr['total'] = $this->total;
        if ($this->skPrice) $arr['sk_price'] = $this->skPrice;

        return $arr;
    }


    public function decInventory(int $num):void
    {
        if ($this->getStatusText() !== '立刻抢') {
            throw new InvalidProductException('无效商品', 1566348619);
        }
        if ($this->inventory - $num < 0) {
            throw new InventoryException('已抢光', 1566283551);
        } else {
            $this->inventory -= $num;
        }
    }

    public function getStatusText():string
    {
        $now = new \DateTime();
        $start = new \DateTime($this->startTime);
        $end = new \DateTime($this->endTime);
        if (($end > $now) && ($now > $start)) {
            if ($this->inventory < 1) {
                return '已抢光';
            } else {
                return '立刻抢';
            }
        }
        if ($now < $start) {
            return '即将开始';
        }

        return '过期';
    }

    public function getProgress():int
    {
        if (new \DateTime() > new \DateTime($this->endTime)) {
            return 100;
        }
        return (($this->total - $this->inventory) / $this->total) * 100;
    }

    public function getSold():int
    {
        return $this->getTotal() - $this->getInventory();
    }

    public function isFinished():bool
    {
        return new \DateTime() > new \DateTime($this->endTime);
    }

    public function getIsFinished():bool
    {
        return $this->isFinished();
    }
}
