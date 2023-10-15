<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity()]
#[ORM\Table(name: 'app_order_item')]
class OrderItem
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    #[ORM\Column(type: Types::STRING)]
    private string $itemPrice;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 1;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;
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

    public function getItemPrice(): Money
    {
        return new Money($this->itemPrice, new \Money\Currency($this->getCurrency()->getCode()));
    }

    public function setItemPrice(Money $itemPrice): static
    {
        $this->itemPrice = $itemPrice->getAmount();
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
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