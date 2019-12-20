<?php

namespace Orq\Laravel\YaCommerce\Domain\Campaign\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\ParameterAttributeTrait;

class QualificationPolicy extends OrmModel implements QualificationPolicyInterface
{
    use ParameterAttributeTrait;

    protected $table = 'yac_qualification_policies';

    /**
     *
     */

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'strategy' => 'required|max:150',
            'parameters' => 'required|max:500',
        ];
    }

    /**
     * Determine the qualification.
     *
     */
    public function isQualified($campaign, $order):bool
    {
        $strategy = resolve($this->strategy);
        return $strategy->isQualified($campaign, $this, $order);
    }
}
