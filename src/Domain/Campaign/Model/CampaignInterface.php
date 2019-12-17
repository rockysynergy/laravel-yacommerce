<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use DateTime;
use Illuminate\Support\Collection;

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
    public function addProduct($product, $campaignPrice = null):void;

    /**
     * Get products attached to the campaign
     */
    public function getProducts():Collection;

    /**
     * Determine whether the campaign has the price policy
     */
    public function hasPricePolicy():bool;

    /**
     * Determine whether the campaign is over
     */
    public function isOver():bool;
}
