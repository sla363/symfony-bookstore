<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;

class CartManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected MoneyManager           $moneyManager,
        protected PriceManager           $priceManager,
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

    public function removeItemFromCart(User $user, Book $book): void
    {
        $cart = $user->getCart();
        $booksInCart = $this->getBooksFromCart($cart);
        if ($booksInCart->contains($book)) {
            $updatedCartItems = $cart->getCartItems()->filter(fn(CartItem $cartItem) => $cartItem->getBook() !== $book);
            $cardItemToRemove = $cart->getCartItems()->filter(fn(CartItem $cartItem) => $cartItem->getBook() === $book)->first();
            $cart->setCartItems(new ArrayCollection($updatedCartItems->toArray()));
            $this->entityManager->remove($cardItemToRemove);

            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }
    }

    public function clearCart(User $user): void
    {
        $cart = $user->getCart();
        foreach ($cart->getCartItems() as $cartItem) {
            $this->entityManager->remove($cartItem);
        }

        $this->entityManager->flush();
    }

    public function getCartItem(User $user, Book $book): ?CartItem
    {
        return $user->getCart()->getCartItems()->filter(fn(CartItem $cartItem) => $cartItem->getBook() === $book)->first() ?: null;
    }

    public function getBooksFromCart(Cart $cart): ReadableCollection
    {
        return $cart->getCartItems()->map(fn(CartItem $cartItem) => $cartItem->getBook());
    }

    public function getTotalSumInCart(Cart $cart): Money
    {
        $books = $this->getBooksFromCart($cart);
        $selectedCurrency = $cart->getUser()->getSelectedCurrency();
        $total = new Money(0, new Currency($selectedCurrency->getCode()));
        foreach ($books as $book) {
            /** @var Book $book */
            $total = $total->add($this->priceManager->getBookPrice($book, $cart->getUser()));
        }

        return $total;
    }

    public function getTotalSumInCartDisplayPrice(Cart $cart): float
    {
        $total = $this->getTotalSumInCart($cart);
        return $this->moneyManager->getFormattedPrice($total);
    }
}