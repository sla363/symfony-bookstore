<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_cart')]
class Cart
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToOne(mappedBy: 'cart', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @var Collection<int, CartItem> $cartItems
     */
    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class)]
    private Collection $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
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

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    /**
     * @param Collection<int, CartItem> $cartItems
     */
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
            $cartItem->setCart($this);
        }
        return $this;
    }

}