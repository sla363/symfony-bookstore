<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testOrderAddOrderItem(): void
    {
        $order = new Order();
        $orderItem = (new OrderItem())
            ->setQuantity(10);
        $order->addOrderItem($orderItem);
        $this->assertEquals($orderItem, $order->getOrderItems()->first());

        $secondOrderItem = (new OrderItem())
            ->setQuantity(20);
        $order->addOrderItem($secondOrderItem);
        $orderItems = $order->getOrderItems();
        $this->assertEquals($secondOrderItem, $orderItems->next());
    }
}