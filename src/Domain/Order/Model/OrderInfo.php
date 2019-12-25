<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\Laravel\YaCommerce\Domain\UserInterface;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

class OrderInfo implements OrderInfoInterface
{
    protected $userId;
    protected $orderItems;
    protected $payTotal = 0;
    protected $description = '';

    /**
     * Construct the OrderInfo Object
     */
    public function __construct(array $data)
    {
        $this->userId = $data['user_id'];
        $this->calculatePayTotal($data);
        $this->orderItems = isset($data['order_items']) ? $data['order_items'] : [];
        $this->deriveDescription($data);
    }

    /**
     * Calculate the Pay Total
     */
    protected function calculatePayTotal(array $data):void
    {
        if (isset($data['pay_amount'])) {
            $this->payTotal = $data['pay_amount'];
        } else {
            $total = 0;
            foreach ($data['order_items'] as $item) {
                $total += $item['pay_amount'];
            }
            $this->payTotal = $total;
        }
    }

    /**
     * generate Description
     */
    protected function deriveDescription($data)
    {
        if (isset($data['description'])) {
            $this->description = $data['description'];
        } else {
            if (count($this->orderItems) > 0) {
                $d = $this->orderItems[0]['title'];
                if (count($this->orderItems) > 1) {
                    $d .= '等';
                }

                $this->description = $d;
            }
        }
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    /**
     * @return int
     */
    public function getPayTotal()
    {
        return $this->payTotal;
    }

    /**
     * Update the payTotal and return a new instance
     */
    public function updatePayTotal($payTotal)
    {
        return new self([
            'pay_amount' => $payTotal,
            'description' => $this->description,
            'order_items' => $this->orderItems,
            'user_id' => $this->userId,
        ]);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
