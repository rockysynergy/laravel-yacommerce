<?php

namespace Tests;

trait MakeStringTrait
{

    protected function makeStr(int $len)
    {
        $aStr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $bStr = $aStr;
        while (strlen($bStr) < $len) {
            $start = \rand(0, strlen($aStr));
            $bStr .= \substr($aStr, $start, strlen($aStr)-$start);
        }
        return substr($bStr, 0, $len);
    }
}
