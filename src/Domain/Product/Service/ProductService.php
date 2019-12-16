<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Service;

use Illuminate\Support\Collection;
use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\Product\Model\AbstractProduct;

/**
 * ProductService.
 * Uses Eloquent model
 */
class ProductService implements ProductServiceInterface, CrudInterface
{

    protected $product;

    public function __construct(AbstractProduct $product)
    {
        $this->product = $product;
    }

    public function getAllForShop(int $shopId, array $filter = [], bool $includeTrashed = false): Collection
    {
        return new Collection(['a' => 'av']);
    }

    public function create(array $data):void
    {
        $this->product->createNew($data);
    }

    public function update(array $data):void
    {
        $this->product->updateInstance($data);
    }
}
