<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Service;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Collection;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Variant;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

/**
 * ProductService.
 * Uses Eloquent model
 */
class ProductService extends AbstractCrudService implements ProductServiceInterface, CrudInterface
{

    /**
     * @param string $type the product type Product | Variant
     */
    public function __construct(string $type)
    {
        switch (strtolower($type)) {
            case 'product':
                $this->ormModel = new Product();
                break;
            case 'variant':
                $this->ormModel = new Variant();
                break;
        }
    }

    public function getAllForShop(int $shopId, array $filter = [], bool $includeTrashed = false): Collection
    {
        return new Collection(['a' => 'av']);
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function decInventory(int $prodId, int $num): void
    {
        $product = $this->ormModel::find($prodId);
        if (!$product) throw new IllegalArgumentException(trans('YaCommerce:message.inventory_no-record'), 1576485686);
        $product->decInventory($num);
    }

    /**
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function incInventory(int $prodId, int $num): void
    {
        $product = $this->ormModel::find($prodId);
        if (!$product) throw new IllegalArgumentException(trans('YaCommerce:message.inventory_no-record'), 1576486738);
        $product->incInventory($num);
    }
}
