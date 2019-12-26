<?php

namespace Orq\Laravel\YaCommerce\Domain;

use Orq\Laravel\YaCommerce\IllegalArgumentException;

abstract class AbstractCrudService implements CrudInterface
{

    protected $ormModel;

    public function __construct($ormModel = null)
    {
        $this->ormModel = $ormModel;
    }

    /**
     * @see Orq\Laravel\YaCommerce\Product\Model\ormModel
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function update(array $data): void
    {
        $this->ormModel->updateInstance($data);
    }

    /**
     * @see Orq\Laravel\YaCommerce\Product\Model\ormModel
     */
    public function create(array $data): void
    {
        $this->ormModel->createNew($data);
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function delete(int $id): void
    {
        $this->ormModel->deleteById($id);
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @return Orq\Laravel\YaCommerce\Domain\Product\Model\ormModel
     */
    public function findById(int $id)
    {
        return $this->ormModel->findById($id);
    }
}
