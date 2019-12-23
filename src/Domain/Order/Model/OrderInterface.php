<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;


interface OrderInterface
{
    /**
     * Generate OrederNumber
     */
    public function generateOrderNumber(string $prefix): string;
}
