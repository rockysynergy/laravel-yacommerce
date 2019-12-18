<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Service;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Seckill;

/**
 * SeckillService.
 * Uses Eloquent model
 */
class SeckillService extends AbstractCrudService implements CrudInterface
{
    protected $products = []; // Tackle the problem that annonymous function can not modify parent scope array for create function

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void
    {
        $this->ormModel->createNew($data, function ($aData) {
            if (isset($aData['products'])) {
                $this->products = $aData['products'];
                unset($aData['products']);
            }
            return $aData;
        }, function ($seckill, $data) {
            foreach ($this->products as $cProd) {
                if (count($cProd) == 1) {
                    $seckill->addProduct($cProd[0]);
                } else {
                    $seckill->addProduct($cProd[0], $cProd[1]);
                }
            }
        });
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        $this->ormModel->updateInstance($data, function($data) {
            if (isset($data['products'])) {
                $this->products = $data['products'];
                unset($data['products']);
            }
            return $data;
        }, function ($seckill, $data) {
            $arr = [];
            foreach ($this->products as $k => $cProd) {
                if (count($cProd) == 1) {
                    array_push($arr, $cProd[0]);
                } else {
                    $arr[$cProd[0]] = ['campaign_price' => $cProd[1]];
                }
            }
            $seckill->products()->sync($arr);
        });
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function delete(int $id): void
    {
        $this->ormModel->deleteById($id, function ($seckill) {
            $seckill->products()->detach();
        });
    }
}
