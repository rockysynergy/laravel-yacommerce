<?php

namespace Orq\Laravel\YaCommerce\Events;

use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\MicroGroup\Domain\Model\BonusPoint;
use App\MicroGroup\Domain\Model\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChangeShipnumber
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * ['shiptracking_id', 'shipnumber', 'phone']
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }




    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
