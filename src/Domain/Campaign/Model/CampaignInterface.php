<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use DateTime;
use Illuminate\Support\Collection;
use Orq\Laravel\YaCommerce\Domain\UserInterface;

interface CampaignInterface
{
    /**
     * Get the Campaign start time
     */
    public function getStartTime():DateTime;

    /**
     * Get the Campaign end time
     */
    public function getEndTime():DateTime;

    /**
     * Add product to campaign
     */
    public function addProduct($productId, $campaignPrice = null):void;

    /**
     * Get products attached to the campaign
     */
    public function getProducts():Collection;

    /**
     * Determine whether the campaign is over
     */
    public function isOver():bool;

    /**
     * Count Participate for user
     *
     * @param Orq\Laravel\YaCommerce\Domain\UserInterface $user
     * @return iIlluminate\Support\Collection
     */
    public function getParticipates(UserInterface $user):Collection;

    /**
     * Add participate to campaign
     */
    public function addParticipate(array $participateData):void;

    /**
     * Calculate the price
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return int
     */
    public function calculatePrice($order): int;

    /**
     * determine the qualification
     *
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return bool
     */
    public function isQualified($order): bool;
}
