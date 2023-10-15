<?php

namespace App\Controller;

use App\Form\UserProfileType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        protected UserManager $userManager,
    )
    {
    }

    #[Route(path: '/my-profile', name: 'app_user_profile', methods: Request::METHOD_GET)]
    public function profile(): Response
    {
        return $this->render('profile.html.twig');
    }

    #[Route(path: '/my-profile', name: 'app_user_edit_profile', methods: Request::METHOD_PUT)]
    public function editProfile(Request $request): Response
    {
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
            return $this->redirectToRoute('main_page');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userManager->saveUser($data);
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}