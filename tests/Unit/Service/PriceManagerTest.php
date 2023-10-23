<?php

namespace App\Tests\Unit\Service;

use App\Entity\Book;
use App\Entity\Currency;
use App\Entity\Price;
use App\Entity\User;
use App\Service\PriceManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Money\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PriceManagerTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;
    private PriceManager $priceManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->priceManager = new PriceManager($this->entityManager);
    }


    public function testGetBookPriceReturnsDefaultCurrencyWhenUserIsNull(): void
    {
        $currency = new Currency();
        $currency->setCode('CZK');

        $price = new Price();
        $price->setCurrency($currency);
        $price->setAmount(new Money(100, new \Money\Currency('CZK')));

        $book = new Book();
        $book->setPrices(new ArrayCollection([$price]));

        $currencyRepoMock = $this->createMock(ObjectRepository::class);
        $currencyRepoMock->method('findOneBy')->willReturn($currency);

        $this->entityManager->method('getRepository')->willReturn($currencyRepoMock);
        $result = $this->priceManager->getBookPrice($book, null);
        $this->assertEquals(new Money(100, new \Money\Currency('CZK')), $result);
    }

    public function testGetBookPriceReturnsUserCurrencyWhenUserNotNull(): void
    {
        $user = new User();

        $currency = new Currency();
        $currency->setCode('EUR');

        $price = new Price();
        $price->setCurrency($currency);
        $price->setAmount(new Money(200, new \Money\Currency('EUR')));

        $book = new Book();
        $book->setPrices(new ArrayCollection([$price]));

        $user->setSelectedCurrency($currency);

        $result = $this->priceManager->getBookPrice($book, $user);
        $this->assertEquals(new Money(200, new \Money\Currency('EUR')), $result);
    }

    public function testGetBookPriceReturnsUserCurrencyWhenUserNotNullAndMultiplePricesAreAvailable(): void
    {
        $user = new User();

        $currencyEUR = new Currency();
        $currencyEUR->setCode('EUR');

        $currencyCZK = new Currency();
        $currencyCZK->setCode('CZK');

        $price = new Price();
        $price->setCurrency($currencyEUR);
        $price->setAmount(new Money(200, new \Money\Currency('EUR')));

        $priceCZK = new Price();
        $priceCZK->setCurrency($currencyCZK);
        $priceCZK->setAmount(new Money(300, new \Money\Currency('CZK')));

        $book = new Book();
        $book->setPrices(new ArrayCollection([$price, $priceCZK]));

        $user->setSelectedCurrency($currencyEUR);
        $result = $this->priceManager->getBookPrice($book, $user);
        $this->assertEquals(new Money(200, new \Money\Currency('EUR')), $result);
    }

    public function testGetBookPriceReturnsUserCurrencyWhenUserNotNullAndBookPriceDoesNotMatchUserCurrency(): void
    {
        $user = new User();

        $currencyEUR = new Currency();
        $currencyEUR->setCode('EUR');
        $currencyUSD = new Currency();
        $currencyUSD->setCode('USD');

        $price = new Price();
        $price->setCurrency($currencyEUR);
        $price->setAmount(new Money(200, new \Money\Currency('EUR')));

        $book = new Book();
        $book->setPrices(new ArrayCollection([$price]));

        $user->setSelectedCurrency($currencyUSD);
        $result = $this->priceManager->getBookPrice($book, $user);
        $this->assertEquals(null, $result);
    }

    public function testGetBookPriceReturnsUserCurrencyWhenUserNotNullAndBookPriceDoesNotExist(): void
    {
        $user = new User();

        $currencyEUR = new Currency();
        $currencyEUR->setCode('EUR');

        $price = new Price();
        $price->setCurrency($currencyEUR);
        $price->setAmount(new Money(200, new \Money\Currency('EUR')));

        $book = new Book();

        $user->setSelectedCurrency($currencyEUR);
        $result = $this->priceManager->getBookPrice($book, $user);
        $this->assertEquals(null, $result);
    }
}