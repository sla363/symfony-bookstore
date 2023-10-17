<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;

class OrderManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransactionManager     $transactionManager,
        protected MoneyManager           $moneyManager,
        protected PriceManager           $priceManager,
    )
    {
    }

    public function generateOrderNumber(): string
    {
        do {
            $generatedOrderNumber = 'ON' . str_pad((string)rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            $existingOrder = $this->entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $generatedOrderNumber]);
        } while ($existingOrder);
        return $generatedOrderNumber;
    }

    /**
     * @throws \Exception
     */
    public function placeOrder(User $user): void
    {
        $cart = $user->getCart();
        if (!$cart) {
            throw new \Exception('Cannot place an order with no cart.');
        }
        $cartItems = $cart->getCartItems();
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cannot place an order with no items in the cart.');
        }
        $selectedCurrency = $user->getSelectedCurrency();

        $order = new Order();
        $orderNumber = $this->generateOrderNumber();
        $order->setOrderNumber($orderNumber);
        $orderItems = new ArrayCollection();
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setBook($cartItem->getBook());
            $orderItem->setOrder($order);
            $orderItem->setQuantity(1);
            $orderItem->setCurrency($selectedCurrency);
            $selectedCurrency->addOrderItem($orderItem);
            $orderItem->setItemPrice($this->priceManager->getBookPrice($orderItem->getBook(), $user));
            $this->entityManager->persist($orderItem);
        }
        $order->setOrderItems($orderItems);
        $user->addOrder($order);

        $this->entityManager->persist($selectedCurrency);
        $this->entityManager->persist($user);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $transaction = new Transaction();
        $transaction->setIdentifier($this->transactionManager->generateTransactionIdentifier());
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $orderNumber]);
        $transaction->setOrder($order);
        $order->setTransaction($transaction);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    public function getOrdersForUser(User $user): array
    {
        return $this->entityManager->getRepository(Order::class)->findBy(['user' => $user]);
    }

    public function getTotalPriceForOrder(Order $order): Money
    {
        $orderItems = $order->getOrderItems();
        $currencyCode = $orderItems->first()->getCurrency()->getCode();
        $totalPrice = new Money(0, new \Money\Currency($currencyCode));

        foreach ($orderItems as $orderItem) {
            /** @var OrderItem $orderItem */
            $totalItemPrice = new Money(0, new \Money\Currency($currencyCode));
            $totalItemPrice = $totalItemPrice->add($orderItem->getItemPrice());
            $totalItemPrice = $totalItemPrice->multiply($orderItem->getQuantity());
            $totalPrice = $totalPrice->add($totalItemPrice);
        }

        return $totalPrice;
    }
}