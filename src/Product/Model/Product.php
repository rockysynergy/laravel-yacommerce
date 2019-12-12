<?php
namespace Orq\Laravel\YaCommerce\Product\Model;

use Illuminate\Support\Facades\Log;
use Orq\DddBase\IllegalArgumentException;
use Orq\Laravel\YaCommerce\OrmModel;

class Product extends OrmModel
{
    protected $table = 'fx_members';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected static $rules = [
        'title' => 'required|max:100',
        'coverPic' => 'required|max:300',
        'description' => 'max:20000',
        'price' => 'gte:0',
        'showPrice' => 'gte:0',
        'pictures' => 'max:500',
        'categoryId' => 'gte:0',
        'inventory' => 'gte:0',
        'status' => 'in:0,1',
        'parameters' => 'max:3000',
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
