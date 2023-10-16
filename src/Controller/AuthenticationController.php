<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function register(
        Request                $request,
        EntityManagerInterface $entityManager,
        Security               $security,
        UserManager            $userManager,
    ): Response
    {
        $form = $this->createForm(RegisterFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $formData = $form->getData();
            /** @var string[] $formData */
            $email = $formData['email'];
            $password = $formData['password'];
            $confirmPassword = $formData['confirm_password'];

            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('A user with this email address already exists.'));
            }

            if ($password !== $confirmPassword) {
                $form->get('confirm_password')->addError(new FormError('Passwords do not match.'));
            }

            if ($form->isValid()) {
                $user = $userManager->createUser($email, $password);
                $security->login($user);

                return $this->redirectToRoute('main_page');
            }

        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}