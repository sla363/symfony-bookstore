<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(path: '/my-profile', name: 'app_user_profile', methods: Request::METHOD_GET)]
    public function profile(): Response
    {
        return $this->render('profile.html.twig');
    }
}