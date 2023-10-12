<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity(repositoryClass: BookRepository::class)]
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

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: CartItem::class)]
    private Collection $cartItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }


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

    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function setOrderItems(Collection $orderItems): static
    {
        $this->orderItems = $orderItems;
        return $this;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        try {
            $orderItems = $this->getOrderItems();
        } catch (\Error $e) {
            $orderItems = new ArrayCollection();
        }
        if ($orderItems->isEmpty() || !$orderItems->contains($orderItem)) {
            $orderItems->add($orderItem);
            $this->setOrderItems($orderItems);
            $orderItem->setBook($this);
        }
        return $this;
    }

    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function setCartItems(Collection $cartItems): static
    {
        $this->cartItems = $cartItems;
        return $this;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        try {
            $cartItems = $this->getCartItems();
        } catch (\Error $e) {
            $cartItems = new ArrayCollection();
        }
        if ($cartItems->isEmpty() || !$cartItems->contains($cartItem)) {
            $cartItems->add($cartItem);
            $this->setCartItems($cartItems);
            $cartItem->setBook($this);
        }

        return $this;
    }
}
