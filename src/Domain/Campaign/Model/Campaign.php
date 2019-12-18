<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

/**
 * The `type` field is used to differentiate campaigns
 */
class Campaign extends OrmModel
{
    protected $table = 'yac_campaigns';

    /**
     * Get the related PricePolicy Record
     */
    public function pricePolicy()
    {
        return $this->hasOne(PricePolicy::class, 'campaign_id');
    }

    /**
     * The Products that belongs to the Campaign
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
     * Determines whether the Campaign campaign is over
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
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'start_time' => 'required|max:20',
            'end_time' => 'required|max:20',
            'type' => 'required|max:200'
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
