<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransactionManager     $transactionManager,
    )
    {
    }

    public function generateOrderNumber(): string
    {
        do {
            $generatedOrderNumber = 'ON' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            $existingOrder = $this->entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $generatedOrderNumber]);
        } while ($existingOrder);
        return $generatedOrderNumber;
    }

    /**
     * @throws \Exception
     */
    public function placeOrder(User $user): void
    {
        $cartItems = $user->getCart()->getCartItems();
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cannot place an order with no items in the cart.');
        }

        $order = new Order();
        $orderNumber = $this->generateOrderNumber();
        $order->setOrderNumber($orderNumber);
        $orderItems = new ArrayCollection();
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setBook($cartItem->getBook());
            $orderItem->setOrder($order);
            $orderItem->setQuantity(1);
            $orderItem->setItemPrice($cartItem->getBook()->getPrice());
            $this->entityManager->persist($orderItem);
        }
        $order->setOrderItems($orderItems);

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
}