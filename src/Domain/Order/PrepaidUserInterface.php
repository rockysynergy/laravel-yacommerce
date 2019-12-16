<?php
namespace Orq\Laravel\YaCommerce\Order;

interface PrepaidUserInterface
{
    public function getLeftCredit();
    public function deductCredit($amount);
}
