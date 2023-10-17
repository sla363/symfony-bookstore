<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Service\OrderManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_order')]
class Order
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: false)]
    private string $orderNumber;

    #[ORM\OneToOne(inversedBy: 'order', targetEntity: Transaction::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Transaction $transaction;

    /**
     * @var Collection<int, OrderItem> $orderItems
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems ?? new ArrayCollection();
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
        $orderItems = $this->getOrderItems();
        if ($orderItems->isEmpty() || !$orderItems->contains($orderItem)) {
            $orderItems->add($orderItem);
            $this->setOrderItems($orderItems);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }
}