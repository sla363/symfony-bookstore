<?php

namespace App\Controller;

use App\Service\CartManager;
use App\Entity\Book;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        protected CartManager            $cartManager,
        protected EntityManagerInterface $entityManager,
        protected UserManager            $userManager,
    )
    {
    }

    #[Route(path: '/add-to-cart/{book}', name: 'app_cart_add')]
    public function add(Book $book): Response
    {
        $user = null;
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
        }
        if ($user) {
            $this->cartManager->addItemToCart($user, $book);
        }
        return $this->redirectToRoute('main_page');
    }

    #[Route(path: '/cart', name: 'app_cart_cart')]
    public function cart(): Response
    {
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
            return $this->redirectToRoute('main_page');
        }
        if ($user) {
            //TODO adjust currency retrieval logic
            return $this->render('cart.html.twig', [
                'cart' => $user->getCart(),
                'total' => $this->cartManager->getTotalSumInCartDisplayPrice($user->getCart()),
                'currency' => $this->cartManager->getBooksFromCart($user->getCart())->first()?->getCurrency()->getCode()
            ]);
        }
        return $this->redirectToRoute('main_page');
    }
}