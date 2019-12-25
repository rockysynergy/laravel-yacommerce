<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;

interface QualificationPolicyInterface
{
    /**
     * determine the qualification
     *
     * @param Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignInterface $campaign
     * @param Orq\Laravel\YaCommerce\Domain\Order\Model\OrderInfoInterface $order
     * @return bool
     */
    public function isQualified($campaign, $order):bool;
}
