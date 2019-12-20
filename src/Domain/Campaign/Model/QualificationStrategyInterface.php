<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


interface QualificationStrategyInterface
{
    /**
     * determine the qualification
     *
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignInterface $campaign
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\QualificationPolicyInterface $qualificationPolicy
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInterface $order
     *
     * @return bool
     */
    public function isQualified($campaign, $policy, $order):bool;
}
