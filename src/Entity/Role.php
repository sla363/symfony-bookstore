<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_role')]
class Role
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /**
     * @var Collection<int, User> $users
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userRoles')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users ?? new ArrayCollection();
    }

    /**
     * @param Collection<int, User> $users
     */
    public function setUsers(Collection $users): static
    {
        $this->users = $users;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function addUser(User $user): static
    {
        $users = $this->getUsers();
        if ($users->isEmpty() || !$users->contains($user)) {
            $users->add($user);
            $user->addRole($this);
            $this->setUsers($users);
        }
        return $this;
    }

    public function removeUser(User $user): static
    {
        $users = $this->getUsers();
        if (!$users->isEmpty() && $users->contains($user)) {
            $users->removeElement($user);
            $user->removeRole($this);
            $this->setUsers($users);
        }

        return $this;
    }
}