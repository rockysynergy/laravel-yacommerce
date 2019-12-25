<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Illuminate\Support\Collection;
use Orq\DddBase\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;
use Orq\Laravel\YaCommerce\Domain\UserInterface;

/**
 * The `type` field is used to differentiate campaigns
 */
class Campaign extends OrmModel implements CampaignInterface, PricePolicyInterface, QualificationPolicyInterface, CampaignRepositoryInterface
{
    protected $table = 'yac_campaigns';

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'start_time' => 'required|max:20',
            'end_time' => 'required|max:20',
            'title' => 'required|max:500'
        ];
    }

    /**
     * Get the related PricePolicy Record
     */
    public function pricePolicy()
    {
        return $this->hasOne(PricePolicy::class);
    }

    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return int
     */
    public function calculatePrice($order): int
    {
        return $this->pricePolicy->calculatePrice($order);
    }

    /**
     * Get the related QualificationPolicy Record
     */
    public function qualificationPolicy()
    {
        return $this->hasOne(QualificationPolicy::class);
    }

    /**
     * determine the qualification
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return bool
     */
    public function isQualified($order): bool
    {
        return $this->qualificationPolicy->isQualified($this, $order);
    }

    /**
     * Get the related participates
     */
    public function participates()
    {
        return $this->hasMany(Participate::class);
    }

    /**
     * Count Participate for user
     *
     * @param Orq\Laravel\YaCommerce\Domain\UserInterface $user
     * @return Illuminate\Support\Collection
     */
    public function getParticipates(UserInterface $user): Collection
    {
        return $this->participates()->where('user_id', '=', $user->getId())->get();
    }

    /**
     * Add participate to campaign
     */
    public function addParticipate(array $participateData): void
    {
        $participateData['campaign_id'] = $this->id;
        $pObj = new Participate();
        $participate = $pObj->createNew($participateData);
        $this->participates()->save($participate);
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

    /**
     * Find the campaign by id
     *
     * @return this
     */
    public function findById(int $id)
    {
        $c = self::find($id);
        if (!$c) throw new IllegalArgumentException(trans("YaCommerce::message.cannot-find-campaign"), 1577244877);
    }
}
