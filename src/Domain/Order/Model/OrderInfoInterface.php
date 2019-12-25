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
     * @return int
    public function getUserId();

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
