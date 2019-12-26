<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Illuminate\Support\Collection;

interface CartitemServiceInterface
{
    /**
     * Add item
     *
     * @param array $data ['user_id', 'product_id', 'amount', 'shop_id', 'campaign_id']
     */
    public function addItem(array $data): void;

    /**
     * Delete Item
     *
     * @param int $itemId
     * @return void
     */
    public function deleteItem(int $itemId): void;

    /**
     * Get all items for user
     *
     * @param array $filter ['user_id', 'shop_id']
     * @return Illuminate\Support\Collection
     */
    public function getAllForUser(int $userId, int $shopId): Collection;

    /**
     * Delete items
     *
     * @param array $itemIds
     * @return void
     */
    public function deleteItems(array $itemIds): void;
}
