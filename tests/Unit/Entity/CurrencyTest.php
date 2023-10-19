<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Currency;
use App\Entity\OrderItem;
use App\Entity\Price;
use App\Entity\User;
use Money\Money;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        $this->currency = new Currency();
    }

    public function testCurrencyAddPrice(): void
    {
        $currency = $this->currency;
        $price = (new Price())
            ->setAmount((new Money(100, new \Money\Currency('CZK'))));
        $currency->addPrice($price);
        $this->assertEquals($price, $currency->getPrices()->first());

        $secondPrice = (new Price())
            ->setAmount((new Money(100_000_000, new \Money\Currency('EUR'))));
        $currency->addPrice($secondPrice);
        $prices = $currency->getPrices();
        $this->assertEquals($secondPrice, $prices->next());
    }

    public function testCurrencyAddUser(): void
    {
        $currency = $this->currency;
        $user = (new User())
            ->setEmail('big@boss.net');
        $currency->addUser($user);
        $this->assertEquals($user, $currency->getUsers()->first());

        $secondUser = (new User())
            ->setEmail('hardworking@guy.com');
        $currency->addUser($secondUser);
        $users = $currency->getUsers();
        $this->assertEquals($secondUser, $users->next());
    }

    public function testCurrencyAddOrderItem(): void
    {
        $currency = $this->currency;
        $orderItem = (new OrderItem())
            ->setQuantity(100);
        $currency->addOrderItem($orderItem);
        $this->assertEquals($orderItem, $currency->getOrderItems()->first());

        $secondOrderItem = (new OrderItem())
            ->setQuantity(500);
        $currency->addOrderItem($secondOrderItem);
        $orderItems = $currency->getOrderItems();
        $this->assertEquals($secondOrderItem, $orderItems->next());
    }
}