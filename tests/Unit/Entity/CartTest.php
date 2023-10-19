<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{

    public function testCartAddCartItem(): void
    {
        $cart = (new Cart())
            ->setUser((new User())->setEmail('vasea@pupking.net'));
        $cartItem = (new CartItem())
            ->setBook((new Book())->setTitle('One rules them all.'));
        $cart->addCartItem($cartItem);
        $this->assertEquals($cartItem, $cart->getCartItems()->first());

        $secondCartItem = (new CartItem())
            ->setBook((new Book())->setTitle('If you\'re reading this, maybe you could offer me a job? :-)'));
        $cart->addCartItem($secondCartItem);
        $cartItems = $cart->getCartItems();
        $this->assertEquals($secondCartItem, $cartItems->next());
    }
}