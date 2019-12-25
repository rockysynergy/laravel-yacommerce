<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

interface CartitemServiceInterface
{
    /**
     * @param array $cartItemIds
     * @return void
     */
    public function deleteItems(array $cartItemIds):void;
}
