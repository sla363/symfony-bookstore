<?php

namespace App\Controller;

use App\Entity\Book;
use App\Service\CartManager;
use App\Service\SearchManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    public function __construct(
        protected SearchManager $searchManager,
        protected CartManager   $cartManager,
        protected UserManager   $userManager,
    )
    {
    }

    #[Route(path: '/', name: 'main_page')]
    public function mainPage(): Response
    {
        return $this->render('main_page.html.twig', [
            'books' => $this->searchManager->getAllBooks(),
        ]);
    }

    #[Route(path: '/book/{id}', name: 'book_resource')]
    public function book(Book $book): Response
    {
        $user = null;
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
        }
        return $this->render('book_resource.html.twig', [
            'book' => $book,
            'cartItem' => $user ? $this->cartManager->getCartItem($user, $book) : null,
        ]);
    }

    #[Route(path: '/search', name: 'search_books')]
    public function searchBooks(Request $request): Response
    {
        $query = $request->query->get('query');
        if (!$query) {
            return $this->redirectToRoute('main_page');
        }
        $books = $this->searchManager->searchBooks($query);
        $noResult = count($books) === 0;
        $books = $noResult ? $this->searchManager->getAllBooks() : $books;
        return $this->render('main_page.html.twig', [
            'books' => $books,
            'no_result' => $noResult,
        ]);
    }
}