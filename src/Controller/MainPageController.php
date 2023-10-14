<?php

namespace App\Controller;

use App\Entity\Book;
use App\Service\CartManager;
use App\Service\MoneyManager;
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
        protected MoneyManager  $moneyManager,
    )
    {
    }

    #[Route(path: '/', name: 'main_page', methods: Request::METHOD_GET)]
    public function mainPage(): Response
    {
        return $this->render('main_page.html.twig', [
            'books' => $this->searchManager->getAllBooks(),
        ]);
    }

    #[Route(path: '/books/{id}', name: 'book_resource', methods: Request::METHOD_GET)]
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

    #[Route(path: '/books/search', name: 'search_books', methods: Request::METHOD_GET)]
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