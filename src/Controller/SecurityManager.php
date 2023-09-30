<?php

namespace App\Controller;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class SecurityManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }


    public function getRoleByName(string $roleName): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $roleName]);
    }
}