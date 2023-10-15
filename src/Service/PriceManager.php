<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Currency;
use App\Entity\Price;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;

class PriceManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function getBookPrice(Book $book, ?User $user): ?Money
    {
        if (!$user) {
            $selectedCurrency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'CZK']);
        } else {
            $selectedCurrency = $user->getSelectedCurrency();
        }
        $bookPrices = $book->getPrices();


        foreach ($bookPrices as $bookPrice) {
            /** @var Price $bookPrice */
            if ($bookPrice->getCurrency()->getCode() === $selectedCurrency->getCode()) {
                return $bookPrice->getAmount();
            }
        }

        return null;
    }

    public function getCurrency(?User $user): Currency
    {
        if (!$user) {
            return $this->getDefaultCurrency();
        }

        return $user->getSelectedCurrency();
    }

    public function getDefaultCurrency(): Currency
    {
        return $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'CZK']);
    }
}