<?php

namespace Orq\Laravel\YaCommerce\Domain\Product\Service;

use Illuminate\Support\Collection;

/**
 * The CategoryService.
 * The category related domain logics
 *
 * @author rockysynergy@qq.com
 */
interface ProductServiceInterface {
    /**
     * fetch all categories for shop.
     *
     * @param int $shopId The shop Id
     * @param array $filter
     * @param bool $includeTrashed
     * @return Illuminate\Support\Collection
     */
    public function getAllForShop(int $shopId, array $filter = [], bool $includeTrashed = false): Collection;

    /**
     * Decrease inventory by $num
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function decInventory(int $prodId, int $num):void;

    /**
     * Increase inventory by $num
     *
     * @throws Orq\Laravel\YaCommerce\IllegalArgumentException
     */
    public function incInventory(int $prodId, int $num):void;
}
