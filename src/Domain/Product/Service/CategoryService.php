<?php
namespace Orq\Laravel\YaCommerce\Domain\Product\Service;

use Illuminate\Support\Collection;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Category;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

/**
 * The CategoryService.
 * The category related domain logics
 *
 * @author rockysynergy@qq.com
 */
class CategoryService extends AbstractCrudService implements CategoryServiceInterface
{

    public function __construct(Category $category)
    {
        $this->ormModel = $category;
    }

    /**
     * @see Orq\Laravel\YaCommerce\Product\Model\Category
     * @return Kalnoy\Nestedset\Collection
     *
     */
    public function getAllForShop(int $shopId, array $filter = [], bool $includedTrash = false):Collection
    {
        return $this->ormModel->getAllForShop($shopId, $filter, $includedTrash);
    }

}
