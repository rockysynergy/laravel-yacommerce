<?php
namespace Orq\Laravel\YaCommerce\Shipment\Service;

use Orq\Laravel\YaCommerce\Shipment\Repository\ShipAddressRepository;

class ShipAddressService
{
    public static function userHasAddress(int $userId, int $addressId):bool
    {
        $addresses = ShipAddressRepository::findForUser($userId);
        foreach ($addresses as $address) {
            if ($address->getId() == $addressId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Orq\Laravel\YaCommerce\UserInterface
     */
    public static function getAllForUser($user):array
    {
        return ShipAddressRepository::find([['user_id', '=', $user->getId()]])->toArray();
    }
}
