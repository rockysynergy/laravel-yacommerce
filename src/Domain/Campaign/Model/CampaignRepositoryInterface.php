<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;


interface CampaignRepositoryInterface
{

    /**
     * @param int $id
     * @return Orq\Laravel\YaCommerce\Domain\Campaign\Model\CampaignInterface
     */
    public function findById(int $id);
}
