<?php

namespace Orq\Laravel\YaCommerce\Listeners;

use App\MicroGroup\Domain\Repository\ShareLogRepository;
use App\MicroGroup\Events\Share;
use Orq\Laravel\YaCommerce\Shipment\Service\ShipTrackingService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShipTrackingSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param  BpChange  $event
     * @return void
     */
    public function handleSubscribe($event)
    {
        $data = [
            'shipTrackingId' => $event->data[0],
            'shipnumber' => $event->data[1],
            'phone' => $event->data[2],
        ];

        $shipTrackingService = resolve(ShipTrackingService::class);
        $shipTrackingService->subscribe($data);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Orq\Laravel\YaCommerce\Events\ChangeShipnumber',
            'Orq\Laravel\YaCommerce\Listeners\ShipTrackingSubscriber@handleSubscribe'
        );
    }
}
