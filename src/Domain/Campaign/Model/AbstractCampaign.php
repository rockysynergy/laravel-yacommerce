<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

abstract class AbstractCampaign extends OrmModel
{
        /**
     * Get the related PricePolicy Record
     */
    public function pricePolicy()
    {
        return $this->hasOne(PricePolicy::class, 'campaign_id');
    }

    /**
     * The Products that belongs to the Seckill
     */
    public function products()
    {
        return $this->morphToMany(Product::class, 'campaign', 'yac_campaign_product', 'campaign_id', 'product_id')->withPivot('campaign_price')->withTimestamps();
    }

    /**
     * add product
     *
     * @param int $productId
     * @return void
     */
    public function addProduct($productId, $campaignPrice = null): void
    {
        if (is_null($campaignPrice)) $this->products()->attach($productId);
        else $this->products()->attach($productId, ['campaign_price' => $campaignPrice]);
    }

    /**
     * Fetch the products for the campaign
     */
    public function getProducts(): \Illuminate\Support\Collection
    {
        return $this->products;
    }

    /**
     * Determines whether the Seckill campaign is over
     *
     * @return bool
     */
    public function isOver(): bool
    {
        $ceTime = new \DateTime($this->end_time);
        $now = new \DateTime();

        return $now >= $ceTime;
    }

    /**
     * Add campaign type
     *
     * @return \Orq\Laravel\YaCommerce\Domain\OrmModel
     */
    public function makeInstance(array $data, ?\Orq\Laravel\YaCommerce\Domain\OrmModel $instance = null)
    {
        $instance = parent::makeInstance($data, $instance);
        $instance->campaign_type = $this->campaignType;
        return $instance;
    }

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'start_time' => 'required|max:20',
            'end_time' => 'required|max:20',
        ];
    }

    /**
     * get the start time
     *
     * @return DateTime
     */
    public function getStartTime(): \DateTime
    {
        return new \DateTime($this->start_time);
    }

    /**
     * get the end time
     *
     * @return DateTime
     */
    public function getEndTime(): \DateTime
    {
        return new \DateTime($this->end_time);
    }
}
