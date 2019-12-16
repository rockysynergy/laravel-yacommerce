<?php
namespace Orq\Laravel\YaCommerce\Shipment\Service;

use Orq\DddBase\DomainException;
use Orq\DddBase\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Shipment\Repository\ShipTrackingRepository;
use Exception;

class ShipTrackingService
{
    public function subscribe($data) {
        $httpService = resolve(HttpService::class);
        $res = $httpService->kd100SubscribeTracking($data);

        if ($res['result']) {
            $returnCode = $res['returnCode'];
            if ($returnCode > 699 && $returnCode < 710) {
                $status = 3;
            }
            if ($returnCode >= 600 && $returnCode < 610) {
                $status = 4;
            }

            if ($returnCode == 200) {
                $status = 1;
            }

            ShipTrackingRepository::updateStatus((int) $data['shipTrackingId'], (int) $status);
        }
    }

    public function updateStatus(array $data): void
    {
        // check signature
        $bSig = md5(config('shiptracking.kd100.salt').json_encode($data['param']));
        if ($data['sign'] != $bSig) {
            throw new IllegalArgumentException('Signiture mismatch! mySign: '. $bSig, 1571738157);
        }

        if (!isset($data['param'])) {
            throw new IllegalArgumentException('don\'t have param!', 1571738131);
        }
        $shipnumber = $data['param']['lastResult']['nu'];
        $shipTracking = ShipTrackingRepository::findOneByShipnumber($shipnumber);
        if (is_null($shipTracking)) {
            throw new DomainException("Can not find the tracking record for " . $shipnumber, 1571650621);
        }

        if ($data['param']['lastResult']['state'] == 3) {
            $shipTracking->setTrackingStatus(2);
        }
        $tracking = \json_encode(['state'=>$data['param']['lastResult']['state'], 'data' => $data['param']['lastResult']['data']]);
        $shipTracking->setTracking($tracking);

        ShipTrackingRepository::update($shipTracking);
    }
}
