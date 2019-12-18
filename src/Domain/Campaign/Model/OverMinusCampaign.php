<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


class OverMinusCampaign extends AbstractCampaign implements CampaignInterface
{
    protected $table = 'yac_campaigns';
    protected $campaignType = 'overMinus';

    /**
     * Determines whether the campaign has PricePolicy
     *
     * @return bool
     */
    public function hasPricePolicy(): bool
    {
        return is_null($this->pricePolicy);
    }
}
