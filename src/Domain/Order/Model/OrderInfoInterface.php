<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

/**
 * The value object implement this interface is
 */
interface OrderInfoInterface
{
    /**
     * get the User
     *
     * @return Orq\Laravel\YaCommerce\Domain\UserInterface
     */
    public function getUser();

    /**
     * Calculate the total
     *
     * @return int
     */
    public function getPayTotal();

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems();

}
