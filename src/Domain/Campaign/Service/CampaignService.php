<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Service;

use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\PricePolicy;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\QualificationPolicy;

/**
 * CampaignService
 *
 * Uses Eloquent model. It impletents a simplified Builder pattern
 */
class CampaignService extends AbstractCrudService implements CrudInterface
{
    /**
     * Tackle the problem that annonymous function can not modify parent scope array for create function
     */
    protected $products = [];
    protected $price_policy = [];
    protected $qualification_policy = [];

    /**
     * The anonymous function to Stash away the products data
     */
    protected $purifyData;

    public function __construct($campaign)
    {
        parent::__construct($campaign);

        $this->purifyData = function ($aData) {
            foreach (['products', 'price_policy', 'qualification_policy'] as $field) {
                if (isset($aData[$field])) {
                    $this->$field = $aData[$field];
                    unset($aData[$field]);
                }
            }
            return $aData;
        };
    }

    /**
     * create campaign and its related products
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void
    {
        $this->ormModel->createNew($data, $this->purifyData, function ($campaign, $data) {
            // Add products
            foreach ($this->products as $cProd) {
                if (count($cProd) == 1) {
                    $campaign->addProduct($cProd[0]);
                } else {
                    $campaign->addProduct($cProd[0], $cProd[1]);
                }
            }

            // Add PricePolicy
            if (count($this->price_policy) > 0) {
                $pricePolicy = resolve(PricePolicy::class);
                $this->price_policy['campaign_id'] = $campaign->id;
                $pricePolicy = $pricePolicy->createNew($this->price_policy);
                $campaign->pricePolicy()->save($pricePolicy);
            }

            // Add QualificationPolicy
            if (count($this->qualification_policy) > 0) {
                $qualificationPolicy = resolve(QualificationPolicy::class);
                $qualificationPolicy = $qualificationPolicy->createNew($this->qualification_policy);
                $campaign->qualificationPolicy()->save($qualificationPolicy);
            }
        });
    }

    /**
     * create campaign and its related products
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        $this->ormModel->updateInstance($data, $this->purifyData, function ($campaign, $data) {
            // Sync products
            $arr = [];
            foreach ($this->products as $k => $cProd) {
                if (count($cProd) == 1) {
                    array_push($arr, $cProd[0]);
                } else {
                    $arr[$cProd[0]] = ['campaign_price' => $cProd[1]];
                }
            }
            $campaign->products()->sync($arr);

            // update PricePolicy
            if (count($this->price_policy) > 0) {
                $pricePolicy = resolve(PricePolicy::class);
                $this->price_policy['campaign_id'] = $campaign->id;
                $pricePolicy = $pricePolicy->updateInstance($this->price_policy);
                $campaign->pricePolicy()->save($pricePolicy);
            }

            // Add QualificationPolicy
            if (count($this->qualification_policy) > 0) {
                $qualificationPolicy = resolve(QualificationPolicy::class);
                $qualificationPolicy = $qualificationPolicy->updateInstance($this->qualification_policy);
                $campaign->qualificationPolicy()->save($qualificationPolicy);
            }
        });
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function delete(int $id): void
    {
        $this->ormModel->deleteById($id, function ($campaign) {
            $campaign->products()->detach();
        });
    }


    /**
     * Add participate to campaign
     */
    public function addParticipate(array $participateData): void
    {
        $this->ormModel->addParticipate($participateData);
    }

    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return int
     */
    public function calculatePrice($order): int
    {
        return $this->ormModel->calculatePrice($order);
    }
}
