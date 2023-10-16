<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    public const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    /**
     * @var Collection<int, Role> $userRoles
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'app_user_role')]
    private Collection $userRoles;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Cart::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Cart $cart;

    /**
     * @var Collection<int, Order> $orders
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $selectedCurrency;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @param Collection<int, Role> $userRoles
     */
    public function setUserRoles(Collection $userRoles): static
    {
        $this->userRoles = $userRoles;
        return $this;
    }

    public function addRole(Role $userRole): static
    {
        try {
            $userRoles = $this->userRoles;
        } catch (\Error $error) {
            $userRoles = new ArrayCollection();
        }
        if ($userRoles->isEmpty() || !$userRoles->contains($userRole)) {
            $userRoles->add($userRole);
            $userRole->addUser($this);
            $this->setUserRoles($userRoles);
        }

        return $this;
    }

    public function removeRole(Role $userRole): static
    {
        try {
            $userRoles = $this->userRoles;
        } catch (\Error $error) {
            $userRoles = new ArrayCollection();
        }
        if (!$userRoles->isEmpty() && $userRoles->contains($userRole)) {
            $userRoles->remove($userRole->getId());
            $userRole->removeUser($this);
            $this->setUserRoles($userRoles);
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->getUserRoles()->map(function (Role $role) {
            return $role->getName();
        });
        return $roles->toArray();
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Collection<int, Order> $orders
     */
    public function setOrders(Collection $orders): static
    {
        $this->orders = $orders;
        return $this;
    }

    public function addOrder(Order $order): static
    {
        try {
            $orders = $this->getOrders();
        } catch (\Error $error) {
            $orders = new ArrayCollection();
        }
        if ($orders->isEmpty() || !$orders->contains($order)) {
            $orders->add($order);
            $this->setOrders($orders);
            $order->setUser($this);
        }

        return $this;
    }

    public function getSelectedCurrency(): Currency
    {
        return $this->selectedCurrency;
    }

    public function setSelectedCurrency(Currency $selectedCurrency): static
    {
        $this->selectedCurrency = $selectedCurrency;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}