<?php

namespace App\Service;

use App\Controller\SecurityManager;
use App\Entity\Cart;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        protected SecurityManager             $securityManager,
        protected UserPasswordHasherInterface $passwordHasher,
        protected EntityManagerInterface      $entityManager,
        protected PriceManager                $priceManager,
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

        $roleUser->addUser($user);
        $user->addRole($roleUser);

        $selectedCurrency = $this->priceManager->getDefaultCurrency();
        $user->setSelectedCurrency($selectedCurrency);
        $selectedCurrency->addUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($roleUser);
        $this->entityManager->persist($selectedCurrency);
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

    /**
     * @throws \Exception
     */
    public function getLoggedInUser(?UserInterface $securityUser): ?User
    {
        if (!$securityUser) {
            throw new \Exception('User is not logged in.');
        }
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $securityUser->getUserIdentifier()]);
    }
}