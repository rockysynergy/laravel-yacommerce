<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Orq\Laravel\YaCommerce\Domain\Product\Model\Product;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

class CartItem extends OrmModel
{
    protected $table = 'yac_cartitems';
    protected $guarded = [];
    protected $model = 'cartitem';

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'product_id' => 'required|gte:0',
            'user_id' => 'required|gte:0',
            'shop_id' => 'required|gte:0',
            'amount' => 'required|gte:0',
            'campain_ids' => 'max:50',
        ];
    }

    /**
     * Get the post that owns the comment.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Add item
     * @todo how about has campaign_ids
     *
     * @param array $data ['user_id', 'product_id', 'amount', 'shop_id', 'campaign_id']
     * @return void
     */
    public function addItem(array $data): void
    {
        $cartItems = self::find([['user_id', '=', $data['user_id']], ['product_id', '=', $data['product_id'], ['shop_id', '=', $data['shop_id']]]])->count();

        if ($cartItems < 1) {
            $this->createNew($data);
        }
    }

    /**
     * Find orders
     *
     * @param array $filter ['shop_id', 'user_id']
     * @return Collection
     */
    public function findAllItems(array $filter)
    {
        if (!isset($filter['shop_id']) || !isset($filter['user_id'])) {
            throw new IllegalArgumentException(trans('YaCommerce::message.all-orders-wrong-params'));
        }

        return self::where([['shop_id', '=', $filter['shop_id']], ['user_id', '=', $filter['user_id']]])->get();
    }
}
