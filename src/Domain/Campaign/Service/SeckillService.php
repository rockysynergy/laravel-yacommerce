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
    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void
    {
        $products = [];
        if (isset($data['products'])) {
            $products = $data['products'];
            unset($data['products']);
        }

        $seckill = $this->ormModel->makeInstance($data);
        foreach ($products as $cProd) {
            if (count($cProd) == 1) {
                $seckill->addProduct($cProd[0]);
            } else {
                $seckill->addProduct($cProd[0], $cProd[1]);
            }
        }

        $seckill->save();
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        if (!isset($data['id'])) throw new IllegalArgumentException(trans("YaCommerce::message.update_no-id"), 1576635817);
        try {
            $this->ormModel->validate($data);
            $products = [];
            if (isset($data['products'])) {
                $products = $data['products'];
                unset($data['products']);
            }
            $seckill = Seckill::find($data['id']);
            if (!$seckill) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576635789);

            $arr = [];
            foreach ($products as $k => $cProd) {
                if (count($cProd) == 1) {
                    array_push($arr, $cProd[0]);
                } else {
                    $arr[$cProd[0]] = ['campaign_price' => $cProd[1]];
                }
            }
            $seckill->products()->sync($arr);
            $seckill->save();
        } catch (IllegalArgumentException $e) {
            throw $e;
        }
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
