<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
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
        //TODO fix currency retrieval
        return new Money($this->itemPrice, new \Money\Currency('CZK'));
    }

    public function setItemPrice(Money $itemPrice): static
    {
        $this->itemPrice = $itemPrice->getAmount();
        return $this;
    }

    public function getItemDisplayPrice(): float {
        $isoCurrencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($isoCurrencies);

        return $moneyFormatter->format($this->getItemPrice());
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
}