<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;
use Illuminate\Support\Facades\Log;
use Orq\DddBase\IllegalArgumentException;

class Product extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'id' => ['validId', [['请提供合法的id', 1565332392]]],
        'title' => ['maxStrLen:100', [['商品名称不能多于100个字符', 1565332430]]],
        'coverPic' => ['maxStrLen:300', [['头图不能多于300个字符', 1565332481]]],
        'description' => ['maxStrLen:2000', [['详情不能多于2000个字符', 1565332518]]],
        'price' => ['positiveNumber', [['请提供合法的价格', 1565332557]]],
        'showPrice' => ['positiveNumber', [['请提供合法的价格', 1570762236]]],
        'pictures' => ['maxStrLen:500', [['图片不能多于500个字符', 1565332604]]],
        'categoryId' => ['validId', [['请提供合法的类别id', 1565332642]]],
        'inventory' => ['positiveNumber', [['请提供合法的库存', 1565744941]]],
        'status' => ['inList:0,1', [['请提供合法的状态', 1565840760]]],
        'parameters' => ['maxStrLen:3000', [['参数不能超过3000个字符', 1570606501]]], // Has setMethod
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
        $arr['show_price'] = $this->showPrice ?? $this->price ?? 0;
        if ($this->id) $arr['id'] = $this->id;
        if (!is_null($this->inventory)) $arr['inventory'] = $this->inventory;
        if ($this->parameters) $arr['parameters'] = $this->parameters;

        return $arr;
    }

    public function setParameters($parameters):void
    {
        if (mb_strlen($parameters) > 3000) {
            throw new IllegalArgumentException("参数不能超过3000个字符", 1570606501);
        }
        if (is_array($parameters)) {
            $this->setParameters = json_encode($parameters);
        } else if (strlen($parameters) > 0) {
            $param = json_decode($parameters, true);
            if (is_null($param)) {
                $parameters = str_replace('，', ',', $parameters); // 中文逗号替换成英文逗号
                $parameters = json_encode(explode(',', $parameters));
            }
            $this->parameters = $parameters;
        }
    }

    public function getParameters():array
    {
        $parameters = isset($this->parameters) ? \json_decode($this->parameters, true) : [];
        return $parameters;
    }

    public function decInventory(int $num):void
    {
        if ($this->inventory < $num) {
            throw new InventoryException('库存不够', 1565744840);
        } else {
            $this->inventory -= $num;
        }
    }

}
