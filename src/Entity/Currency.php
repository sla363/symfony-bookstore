<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_currency')]
class Currency
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $code;

    /**
     * @var Collection<int, User> $users
     */
    #[ORM\OneToMany(mappedBy: 'selectedCurrency', targetEntity: User::class)]
    private Collection $users;

    /**
     * @var Collection<int, Price> $prices
     */
    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: Price::class)]
    private Collection $prices;

    /**
     * @var Collection<int, OrderItem> $orderItems
     */
    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }


    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * @param Collection<int, Price> $prices
     */
    public function setPrices(Collection $prices): static
    {
        $this->prices = $prices;
        return $this;
    }

    public function addPrice(Price $price): static
    {
        try {
            $prices = $this->getPrices();
        } catch (\Error $e) {
            $prices = new ArrayCollection();
        }
        if ($prices->isEmpty() || !$prices->contains($price)) {
            $prices->add($price);
            $this->setPrices($prices);
            $price->setCurrency($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection<int, User> $users
     */
    public function setUsers(Collection $users): static
    {
        $this->users = $users;
        return $this;
    }

    public function addUser(User $user): static
    {
        try {
            $users = $this->getUsers();
        } catch (\Error $e) {
            $users = new ArrayCollection();
        }
        if ($users->isEmpty() || !$users->contains($user)) {
            $users->add($user);
            $this->setUsers($users);
            $user->setSelectedCurrency($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @param Collection<int, OrderItem> $orderItems
     */
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
            $orderItem->setCurrency($this);
        }

        return $this;
    }
}