<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Service;

use Orq\DddBase\ModelFactory;
use Orq\Laravel\YaCommerce\Domain\Order\Model\CartItem;
use Orq\Laravel\YaCommerce\Domain\Order\Repository\CartItemRepository;

class CartItemService
{

    public static function addItem(int $userId, array $data):void
    {
        $cartItems = CartItemRepository::find([['user_id', '=', $userId], ['product_id', '=', $data['product_id']]])->count();

        if ($cartItems < 1) {
            $item = ModelFactory::make(CartItem::class, array_merge(['user_id'=>$userId], $data));
            CartItemRepository::save($item);
        }
    }

    public static function deleteItem(int $itemId):void
    {
        CartItemRepository::removeById($itemId);
    }

    public static function getAllForUser(int $userId, int $shopId):array
    {
        return CartItemRepository::findAllForUser($userId, $shopId);
    }
}
