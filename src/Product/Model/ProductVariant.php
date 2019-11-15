<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Orq\DddBase\IdTrait;
use Orq\DddBase\AbstractEntity;
use Orq\DddBase\IllegalArgumentException;

class ProductVariant extends AbstractEntity
{
    use IdTrait;

    protected $fieldsConf = [
        'productId' => ['validId', [['请提供合法的产品id', 1570608626]]],
        'model' => ['maxStrLen:100', [['型号名称不能多于100个字符', 1570608661]]],
        'status' => ['inList:0,1', [['请提供合法的状态', 1570609239]]],
        'price' => ['positiveNumber', [['请提供合法的价格', 1570608819]]],
        'showPrice' => ['positiveNumber', [['请提供合法的价格', 1570762372]]],
        'inventory' => ['positiveNumber', [['请提供合法的库存', 1570609275]]],
        'pictures' => ['maxStrLen:500', [['图片不能多于500个字符', 1570608830]]],
        'parameters' => ['maxStrLen:3000', [['参数不能超过3000个字符', 1570608843]]], // Has setMethod
    ];

    public function getPersistData():array
    {
        $arr = [];
        $arr['product_id'] = $this->productId;
        if (isset($this->status)) $arr['status'] = $this->status;
        if ($this->model) $arr['model'] = $this->model;
        if ($this->description) $arr['description'] = $this->description;
        if (isset($this->price)) $arr['price'] = $this->price;
        $arr['show_price'] = $this->showPrice ?? $this->price ?? 0;
        if (isset($this->inventory)) $arr['inventory'] = $this->inventory;
        if ($this->pictures) $arr['pictures'] = $this->pictures;
        if ($this->id) $arr['id'] = $this->id;
        if ($this->parameters) $arr['parameters'] = $this->parameters;

        return $arr;
    }

    public function setParameters($parameters):void
    {
        if (mb_strlen($parameters) > 3000) {
            throw new IllegalArgumentException("参数不能超过3000个字符", 1570608843);
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
            throw new InventoryException('库存不够', 1570609318);
        } else {
            $this->inventory -= $num;
        }
    }
}
