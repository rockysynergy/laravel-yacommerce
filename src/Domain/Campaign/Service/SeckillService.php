<?php
namespace Orq\Laravel\YaCommerce\Domain\Campaign\Service;

use Illuminate\Support\Collection;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;

/**
 * SeckillService.
 * Uses Eloquent model
 */
class SeckillService extends AbstractCrudService implements CrudInterface
{
    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data):void
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
}
