<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\User;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function addItemToCart(User $user, Book $book): void
    {
        $cart = $user->getCart();
        $booksInCart = $this->getBooksFromCart($cart);
        if (!$booksInCart->contains($book)) {
            $cartItem = new CartItem();
            $cartItem->setBook($book);
            $cartItem->setCart($cart);
            $book->addCartItem($cartItem);
            $cart->addCartItem($cartItem);
            $cartItem->setCart($cart);

            $this->entityManager->persist($book);
            $this->entityManager->persist($cart);
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        }
    }

    public function getBooksFromCart(Cart $cart): ReadableCollection
    {
        return $cart->getCartItems()->map(fn(CartItem $cartItem) => $cartItem->getBook());
    }
}