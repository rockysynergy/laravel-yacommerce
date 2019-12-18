<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Service;

use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\PricePolicy;

/**
 * AbstractCampaignService
 *
 * Uses Eloquent model. It impletents a simplified Builder pattern
 */
class AbstractCampaignService extends AbstractCrudService implements CrudInterface
{
    /**
     * Tackle the problem that annonymous function can not modify parent scope array for create function
     */
    protected $products = [];
    protected $price_policy = [];

    /**
     * The anonymous function to Stash away the products data
     */
    protected $purifyData;

    public function __construct($seckill)
    {
        parent::__construct($seckill);

        $this->purifyData = function ($aData) {
            foreach (['products', 'price_policy'] as $field) {
                if (isset($aData[$field])) {
                    $this->$field = $aData[$field];
                    unset($aData[$field]);
                }
            }
            return $aData;
        };
    }


    /**
     * create seckill and its related products
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void
    {
        $this->ormModel->createNew($data, $this->purifyData, function ($seckill, $data) {
            // Add products
            foreach ($this->products as $cProd) {
                if (count($cProd) == 1) {
                    $seckill->addProduct($cProd[0]);
                } else {
                    $seckill->addProduct($cProd[0], $cProd[1]);
                }
            }

            // Add PricePolicy
            if (count($this->price_policy) > 0) {
                $pricePolicy = resolve(PricePolicy::class);
                $pricePolicy = $pricePolicy->createNew($this->price_policy);
                $seckill->pricePolicy()->save($pricePolicy);
            }
        });
    }

    /**
     * create seckill and its related products
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        $this->ormModel->updateInstance($data, $this->purifyData, function ($seckill, $data) {
            // Sync products
            $arr = [];
            foreach ($this->products as $k => $cProd) {
                if (count($cProd) == 1) {
                    array_push($arr, $cProd[0]);
                } else {
                    $arr[$cProd[0]] = ['campaign_price' => $cProd[1]];
                }
            }
            $seckill->products()->sync($arr);

            // update PricePolicy
            if (count($this->price_policy) > 0) {
                $name = ucfirst($this->price_policy['type']) . 'PricePolicy';
                $pricePolicy = new $name();
                $pricePolicy = $pricePolicy->updateInstance($this->price_policy);
                $seckill->pricePolicy()->save($pricePolicy);
            }
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
