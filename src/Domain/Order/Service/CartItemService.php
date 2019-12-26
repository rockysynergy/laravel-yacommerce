<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Service;

use Orq\DddBase\ModelFactory;
use Illuminate\Support\Collection;
use Orq\Laravel\YaCommerce\Domain\CrudInterface;
use Orq\Laravel\YaCommerce\Domain\AbstractCrudService;
use Orq\Laravel\YaCommerce\Domain\Order\Model\CartItem;

class CartItemService extends AbstractCrudService implements CrudInterface
{

    /**
     * Add item
     *
     * @param array $data ['user_id', 'product_id', 'amount', 'shop_id', 'campaign_id']
     * @return void
     */
    public function addItem(array $data):void
    {
        $this->ormModel->addItem($data);
    }

    /**
     * Get all items for user
     *
     * @param array $filter ['user_id', 'shop_id']
     * @return Illuminate\Support\Collection
     */
    public function getAllForUser(array $filter):Collection
    {
        return $this->ormModel->findAllItems($filter);
    }

    /**
     * Delete items
     *
     * @param array $itemIds
     * @return void
     */
    public function deleteItems(array $itemIds):void
    {
        foreach ($itemIds as $id) {
            $this->deleteItem($id);
        }
    }

    /**
     * Delete Item
     *
     * @param int $itemId
     * @return void
     */
    public function deleteItem(int $itemId):void
    {
        $this->ormModel->deleteById($itemId);
    }
}
