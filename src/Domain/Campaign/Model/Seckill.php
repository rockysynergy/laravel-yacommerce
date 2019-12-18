<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;

class Seckill extends AbstractCampaign implements CampaignInterface
{
    use AttachProductTrait;

    protected $table = 'yac_campaigns';
    protected $campaignType = 'seckill';


    /**
     * The Products that belongs to the Seckill
     */
    public function products()
    {
        return $this->morphToMany(Product::class, 'campaign', 'yac_campaign_product', 'campaign_id', 'product_id')->withPivot('campaign_price')->withTimestamps();
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
    public function isOver():bool
    {
        $ceTime = new \DateTime($this->end_time);
        $now = new \DateTime();

        return $now >= $ceTime;
    }

    /**
     * Determines whether the campaign has PricePolicy
     *
     * @return bool
     */
    public function hasPricePolicy(): bool
    {
        return false;
    }
}