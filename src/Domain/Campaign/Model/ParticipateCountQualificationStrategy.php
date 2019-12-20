<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


/**
 * This strategy uses count of user's participates to determine the qualification
 *
 * It requires `participate_limits`
 */
class ParticipateCountQualificationStrategy implements QualificationStrategyInterface
{

    /**
     * Determine the qualification
     *
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignInterface $campaign
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\QualificationPolicyInterface $pricePolicy
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInterface $order
     * @return int
     */
    public function isQualified($campaign, $policy, $order): bool
    {
        $participates = $campaign->getParticipates($order->getUser())->count();
        return $participates < $policy->parameters['participate_limits'];
    }
}
