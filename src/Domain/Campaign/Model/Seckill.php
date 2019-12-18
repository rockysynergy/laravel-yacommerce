<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


class Seckill extends AbstractCampaign implements CampaignInterface
{
    protected $table = 'yac_campaigns';
    protected $campaignType = 'seckill';

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
