<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(path: '/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        return $this->render('profile.html.twig');
    }
}