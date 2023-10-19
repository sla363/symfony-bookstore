<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\OrderItem;
use App\Entity\Price;
use App\Entity\User;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    private ?Book $book;

    protected function setUp(): void
    {
        $this->book = new Book();
    }

    protected function tearDown(): void
    {
        $this->book = null;
    }

    public function testBookAddOrderItem(): void
    {
        $book = $this->book;
        $this->assertNotNull($book);
        $orderItem = (new OrderItem())
            ->setQuantity(10)
            ->setItemPrice(new Money(100, new Currency('CZK')));
        $book->addOrderItem($orderItem);
        $this->assertEquals($orderItem, $book->getOrderItems()->first());
        $this->assertEquals($book, $orderItem->getBook());

        $secondOrderItem = (new OrderItem())
            ->setQuantity(20)
            ->setItemPrice(new Money(2000, new Currency('EUR')));
        $book->addOrderItem($secondOrderItem);
        $orderItems = $book->getOrderItems();
        $this->assertEquals($secondOrderItem, $orderItems->next());
    }

    public function testBookAddCartItem(): void
    {
        $book = $this->book;
        $this->assertNotNull($book);
        $cartItem = (new CartItem())
            ->setCart((new Cart())->setUser((new User())->setEmail('test@email.com')));
        $book->addCartItem($cartItem);
        $this->assertEquals($cartItem, $book->getCartItems()->first());

        $secondCartItem = (new CartItem())
            ->setCart((new Cart())->setUser((new User())->setEmail('another@test.cz')));
        $book->addCartItem($secondCartItem);
        $cartItems = $book->getCartItems();
        $this->assertEquals($secondCartItem, $cartItems->next());
    }

    public function testBookAddPrice(): void
    {
        $book = $this->book;
        $this->assertNotNull($book);
        $price = (new Price())
            ->setAmount(new Money(100, new Currency('CZK')));
        $book->addPrice($price);
        $this->assertEquals($price, $book->getPrices()->first());

        $newPrice = (new Price())
            ->setAmount(new Money(2000, new Currency('USD')));
        $book->addPrice($newPrice);
        $prices = $book->getPrices();
        $this->assertEquals($newPrice, $prices->next());
    }
}