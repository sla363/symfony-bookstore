<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(UserPasswordHasherInterface $passwordHasher, Request $request, SecurityManager $securityManager, EntityManagerInterface $entityManager): Response {

        $form = $this->createForm(RegisterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user = new User();

            $user->setEmail($formData['email']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData['password']
            );
            $user->setPassword($hashedPassword);

            $roleUser = $securityManager->getRoleByName(User::ROLE_USER);
            if (!$roleUser) {
                throw new \Exception('User role does not exist, cannot create user.');
            }
            $roleUser->addUser($user);
            $user->addRole($roleUser);
            $entityManager->persist($roleUser);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}