<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;

abstract class AbstractCampaign extends OrmModel
{
    /**
     * Make validation rules for the model
     */
    protected function makeRules():array
    {
        return [
            'start_time' => 'required|max:20',
            'end_time' => 'required|max:20',
        ];
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

}
