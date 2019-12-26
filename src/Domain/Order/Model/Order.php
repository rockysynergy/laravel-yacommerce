<?php
namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\Laravel\YaCommerce\Domain\OrmModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

class Order extends OrmModel implements OrderInterface
{

    use SoftDeletes;
    protected $table = 'yac_orders';
    protected $model = 'order';
    protected $guarded = ['order_number_prefix', 'type'];

    /**
     * Set the default value
     */
    protected $attributes = [
        'pay_status' => 1,
        'pay_method' => 1,
    ];

    /**
     * Make validation rules for the model
     */
    protected function makeRules(): array
    {
        return [
            'order_number' => 'min:20|max:20',
            'exorder_number' => 'max:30',
            'pay_amount' => 'numeric',
            'pay_status' => 'in:1,2',
            'pay_method' => 'in:1,2,3',
            'pay_time' => 'max:20',
            'user_id' => 'required|gte:0',
            'shop_id' => 'required|gte:0',
            'description' => 'max:1000',
        ];
    }

    /**
     * Generate OrederNumber
     */
    public function generateOrderNumber(string $prefix):string
    {
        $orderNoPrefix = strtoupper($prefix);
        if (preg_match('/[A-Z][A-Z]/', $orderNoPrefix) !== 1) {
            throw new IllegalArgumentException('订单号前缀必须是2个英文字符！', 1564728410);
        }
        do {
            $orderNumber = $orderNoPrefix . date('YmdHis') . rand(1000, 9999);
        } while (self::where('order_number', '=', $orderNumber)->count() > 0);

        return $orderNumber;
    }

    /**
     * get the pay status label
     */
    public function getPayStatusLabelAttribute(): string
    {
        return config("pay.pay_status.{$this->pay_status}");
    }

    /**
     * get the pay status label
     */
    public function getPayMethodLabelAttribute(): string
    {
        return config("pay.pay_status.{$this->pay_method}");
    }

    /**
     * Get the orderitems for the order
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Add orderItem
     */
    public function addItem(array $data)
    {
        $data['order_id'] = $this->id ;
        $pObj = new OrderItem();
        $orderItem = $pObj->createNew($data);
        $this->orderItems()->save($orderItem);
    }

    /**
     * Find orders
     *
     * @param array $filter ['shop_id', 'user_id']
     * @return Collection
     */
    public function findAllOrders(array $filter)
    {
        if (!isset($filter['shop_id']) || !isset($filter['user_id'])) {
            throw new IllegalArgumentException(trans('YaCommerce::message.all-orders-wrong-params'));
        }

        return self::where([['shop_id', '=', $filter['shop_id']], ['user_id', '=', $filter['user_id']]])->get();
    }
}
