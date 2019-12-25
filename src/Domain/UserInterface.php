<?php

namespace Orq\Laravel\YaCommerce\Domain;

interface UserInterface
{
    /**
     * @param int $userId
     * @return string
     */
    public function getWxOpenId():string;
}
