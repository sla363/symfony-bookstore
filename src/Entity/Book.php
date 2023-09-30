<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

#[ORM\Entity()]
#[ORM\Table(name: 'app_book')]
class Book
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $publishedDate;

    #[ORM\Column(length: 255)]
    private string $isbn;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $price;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private Author $author;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private Genre $genre;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublishedDate(): \DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(\DateTimeImmutable $publishedDate): static
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDisplayPrice(): float {
        $money = new Money($this->price, new \Money\Currency($this->getCurrency()->getCode()));
        $isoCurrencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($isoCurrencies);

        return $moneyFormatter->format($money);
    }

    public function getPrice(): Money
    {
        return new Money($this->price, new \Money\Currency($this->getCurrency()->getCode()));
    }

    public function setPrice(Money $price): static
    {
        $this->price = $price->getAmount();
        return $this;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): static
    {
        $this->genre = $genre;

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
