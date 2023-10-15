<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity()]
#[ORM\Table(name: 'app_price')]
class Price
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $amount;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): Money
    {
        return new Money($this->amount, new \Money\Currency($this->getCurrency()->getCode()));
    }

    public function setAmount(Money $amount): static
    {
        $this->amount = $amount->getAmount();
        return $this;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): static
    {
        $this->book = $book;
        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static
    {
        $this->currency = $currency;
        return $this;
    }
}