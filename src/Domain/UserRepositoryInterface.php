<?php

namespace Orq\Laravel\YaCommerce\Domain;

interface UserRepositoryInterface
{
    /**
     * @param int $userId
     * @return Orq\Laravel\YaCommerce\Domain\UserInterface | null
     */
    public function findById(int $id);
}
