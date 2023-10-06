<?php

namespace App\Service;

use App\Controller\SecurityManager;
use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        protected SecurityManager             $securityManager,
        protected UserPasswordHasherInterface $passwordHasher,
        protected EntityManagerInterface      $entityManager,
    )
    {
    }

    public function createUser(string $email, #[SensitiveParameter] $password): User
    {
        $user = new User();

        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);

        $roleUser = $this->securityManager->getRoleByName(User::ROLE_USER);
        if (!$roleUser) {
            throw new \Exception('User role does not exist, cannot create user.');
        }


        $this->entityManager->persist($roleUser);
        $this->entityManager->persist($user);
        $roleUser->addUser($user);
        $user->addRole($roleUser);
        $this->entityManager->flush();

        $cart = new Cart();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $user->setCart($cart);
        $cart->setUser($user);
        $this->entityManager->persist($cart);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}