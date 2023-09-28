<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: "/", name: "main_page")]
    public function mainPage(): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        return $this->render("main_page.html.twig", [
            'books' => $books,
        ]);
    }
}