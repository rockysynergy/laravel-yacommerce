<?php

namespace Orq\Laravel\YaCommerce\Domain;

/**
 * Create, Update and Delete model
 *  *
 * @author rockysynergy@qq.com
 */
interface CrudInterface {
    /**
     * Create and insert the instance to DB
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function create(array $data): void;

    /**
     * persist change of the instance to DB
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void;

    /**
     * delete the instance
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function delete(int $id): void;

    /**
     * fetch the instance by id
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @returns Object
     */
    public function findById(int $id);
}
