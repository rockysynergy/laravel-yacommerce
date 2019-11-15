<?php

namespace Tests\YaCommerce\Unit\Shipment;

use Tests\DbTestCase;
use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Shipment\Service\HttpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orq\Laravel\YaCommerce\Shipment\Service\ShipTrackingService;

class ShipTrackingServiceTest extends DbTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function subscribeUpdatesTrackingStatus()
    {
        $shipTrackingId = 5;
        $shipnumber = 'ST20191021002';
        $phone = '13988766713';
        $data = [
            'shipTrackingId' => $shipTrackingId,
            'shipnumber' => $shipnumber,
            'phone' => $phone
        ];

        DB::table('yac_shiptrackings')->insert(['id' => $shipTrackingId, 'shipnumber' => $shipnumber, 'shipaddress_id' => 3, 'order_id' => 15]);
        $this->mock(HttpService::class, function ($mock) {
            $mock->shouldReceive('kd100SubscribeTracking')->once()
                ->andReturn(['result' => true, 'returnCode' => 200, 'message' => '提交成功']);
        });

        $shipTrackingService = new ShipTrackingService();
        $shipTrackingService->subscribe($data);
        $this->assertDatabaseHas('yac_shiptrackings', ['shipnumber' => $shipnumber, 'tracking_status' => 1]);
    }

    /**
     * @test
     */
    public function updateStatus()
    {
        $data = [];
        $shipnumber = 'V030344422';
        $data['param'] = [
            'status' => 'polling',
            'lastResult' => [
                'state' => '0',
                'nu' => $shipnumber,
                'data' => [
                    [
                        "context" => "上海分拨中心出库",
                        "time" => "2012-08-28 16:33:19",
                        "ftime" => "2012-08-28 16:33:19",
                        "status" => "在途",
                        "areaCode" => "310000000000",
                        "areaName" => "上海市",
                    ],
                    [
                        "context" => "上海分拨中心入库",
                        "time" => "2012-08-27 23:22:42",
                        "ftime" => "2012-08-27 23:22:42",
                        "status" => "在途",
                        "areaCode" => "310000000000",
                        "areaName" => "上海市",
                    ],
                ]
            ]
        ];
        $data['sign'] = md5(config('shiptracking.kd100.salt').json_encode($data['param']));

        $shipTrackingId = 5;
        DB::table('yac_shiptrackings')->insert(['id' => $shipTrackingId, 'shipnumber' => $shipnumber, 'shipaddress_id' => 3, 'order_id' => 15]);
        $tracking = DB::table('yac_shiptrackings')->where('id', $shipTrackingId)->first();
        $this->assertNull($tracking->tracking);

        $shipTrackingService = new ShipTrackingService();
        $shipTrackingService->updateStatus($data);
        $tracking = DB::table('yac_shiptrackings')->where('id', $shipTrackingId)->first();
        $this->assertNotNull($tracking->tracking);
    }
}
