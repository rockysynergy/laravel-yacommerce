<?php

namespace Orq\Laravel\YaCommerce\Order\Repository;

use Illuminate\Support\Facades\DB;
use Orq\Laravel\YaCommerce\Order\Model\CartItem;
use Orq\DddBase\Repository\AbstractRepository;
use App\MicroGroup\Domain\IllegalArgumentException;
use Orq\Laravel\YaCommerce\Product\Repository\ProductRepository;

class CartItemRepository extends AbstractRepository
{
    protected static $table = 'yac_cartitems';
    protected static $class = CartItem::class;


    /**
     */
    public static function findAllForUser(int $userId, int $shopId):array
    {
        if ($userId < 0) {
            throw new IllegalArgumentException('请提供合法的用户id!', 1566029122);
        }
        $itmes = self::find([['user_id', '=', $userId], ['shop_id', '=', $shopId]])->toArray();
        foreach ($itmes as $k=>$item) {
            $product = ProductRepository::findById($item['product_id']);
            unset($product['description']);
            unset($product['pictures']);
            $itmes[$k]['product'] = $product;
        }
        return $itmes;
    }

    public static function deleteItems(array $ids):void
    {
        DB::table(self::$table)->whereIn('id', $ids)->delete();
    }
}
