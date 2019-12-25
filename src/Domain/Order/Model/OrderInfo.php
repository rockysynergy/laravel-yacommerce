<?php

namespace Orq\Laravel\YaCommerce\Domain\Order\Model;

use Orq\Laravel\YaCommerce\Domain\UserInterface;
use Orq\Laravel\YaCommerce\IllegalArgumentException;

class OrderInfo implements OrderInfoInterface
{
    protected $user;
    protected $orderItems;
    protected $payTotal = 0;

    /**
     * Construct the OrderInfo Object
     */
    public function __construct(array $data, UserInterface $user)
    {
        $this->user = $user;
        $this->calculatePayTotal($data);
        $this->orderItems = isset($data['order_items']) ? $data['order_items'] : [];
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
     * @return Orq\Laravel\YaCommerce\Domain\UserInterface
     */
    public function getUser()
    {
        return $this->user;
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
}
