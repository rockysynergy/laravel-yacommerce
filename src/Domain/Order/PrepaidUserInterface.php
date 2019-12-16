<?php
namespace Orq\Laravel\YaCommerce\Domain\Order;

interface PrepaidUserInterface
{
    public function getLeftCredit();
    public function deductCredit($amount);
}
