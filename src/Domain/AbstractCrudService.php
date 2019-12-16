<?php

namespace Orq\Laravel\YaCommerce\Domain;

use Orq\Laravel\YaCommerce\IllegalArgumentException;

abstract class AbstractCrudService implements CrudInterface
{

    protected $ormModel;

    public function __construct($ormModel)
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
        $ormModel = $this->ormModel::find($id);
        if (!$ormModel) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576480164);
        $ormModel->delete();
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     * @return Orq\Laravel\YaCommerce\Domain\Product\Model\ormModel
     */
    public function findById(int $id)
    {
        $ormModel = $this->ormModel::find($id);
        if (!$ormModel) throw new IllegalArgumentException(trans("YaCommerce::message.no-record"), 1576480181);
        return $ormModel;
    }
}
