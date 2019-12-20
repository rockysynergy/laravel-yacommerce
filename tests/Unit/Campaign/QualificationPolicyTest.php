<?php

namespace Tests\YaCommerce\Unit\Campaign;

use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\Campaign;
use Orq\Laravel\YaCommerce\Domain\Order\Model\Order;
use Orq\Laravel\YaCommerce\Domain\Campaign\Model\QualificationPolicy;
use Orq\Laravel\YaCommerce\Domain\UserInterface;

class QualificationPolicyTest extends TestCase
{
    /**
     * @test
     */
    public function participateCountQualificationStrategy()
    {
        $qualificationPolicy = new QualificationPolicy();
        $qualificationPolicy->strategy = '\Orq\Laravel\YaCommerce\Domain\Campaign\Model\ParticipateCountQualificationStrategy';
        $parameters = [
            'participate_limits' => 1
        ];
        $qualificationPolicy->parameters = $parameters;

        $userId = 309;
        $user = $this->mock(UserInterface::class, function ($mock) use ($userId) {
            $mock->shouldReceive('getId')->andReturn($userId);
        });
        $order = $this->mock(Order::class, function ($mock) use ($user) {
            $mock->shouldReceive('getUser')->andReturn($user);
        });
        $campaign = $this->mock(Campaign::class, function ($mock) use ($user) {
            $mock->shouldReceive('getParticipates')
                ->with($user)
                ->andReturn(new Collection(['a', 'b']));
        });

        $this->assertFalse($qualificationPolicy->isQualified($campaign, $order));
    }
}
