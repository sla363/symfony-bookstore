<?php

namespace App\Controller;

use App\Service\CartManager;
use App\Entity\Book;
use App\Service\OrderManager;
use App\Service\PriceManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        protected CartManager            $cartManager,
        protected EntityManagerInterface $entityManager,
        protected UserManager            $userManager,
        protected OrderManager           $orderManager,
        protected PriceManager           $priceManager,
    )
    {
    }

    #[Route(path: '/cart/{book}', name: 'app_cart_add', methods: Request::METHOD_POST)]
    public function add(Request $request, Book $book): Response
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

        if ($request->get('redirect_to_route') && is_string($request->get('redirect_to_route'))) {
            return $this->redirectToRoute($request->get('redirect_to_route'), ['id' => $book->getId()]);
        }
        return $this->redirectToRoute('main_page');
    }

    #[Route(path: '/cart/{book}', name: 'app_cart_remove', methods: Request::METHOD_DELETE)]
    public function remove(Request $request, Book $book): Response
    {
        $user = null;
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
        }
        if ($user) {
            $this->cartManager->removeItemFromCart($user, $book);
        }

        if ($request->get('redirect_to_route') && is_string($request->get('redirect_to_route'))) {
            return $this->redirectToRoute($request->get('redirect_to_route'), ['id' => $book->getId()]);
        }
        return $this->redirectToRoute('app_cart_cart');
    }

    #[Route(path: '/cart', name: 'app_cart_clear_cart', methods: Request::METHOD_DELETE)]
    public function clearCart(): Response
    {
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
            return $this->redirectToRoute('main_page');
        }
        if ($user) {
            $this->cartManager->clearCart($user);
        }
        return $this->redirectToRoute('app_cart_cart');
    }

    #[Route(path: '/cart', name: 'app_cart_cart', methods: Request::METHOD_GET)]
    public function cart(): Response
    {
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
            return $this->redirectToRoute('main_page');
        }
        if ($user) {
            return $this->render('cart.html.twig', [
                'cart' => $user->getCart(),
                'total' => $user->getCart()->getCartItems()->isEmpty() ? 0 : $this->cartManager->getTotalSumInCartDisplayPrice($user->getCart()),
                'currency' => $this->priceManager->getCurrency($user)->getCode()
            ]);
        }
        return $this->redirectToRoute('main_page');
    }
}