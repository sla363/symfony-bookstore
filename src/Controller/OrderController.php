<?php

namespace App\Controller;

use App\Service\CartManager;
use App\Service\OrderManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(
        protected OrderManager $orderManager,
        protected UserManager  $userManager,
        protected CartManager  $cartManager,
    )
    {
    }


    #[Route(path: '/place-order', name: 'app_order_place_order')]
    public function placeOrder(): Response
    {
        $user = null;
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
        }
        if ($user) {
            try {
                $this->orderManager->placeOrder($user);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            $this->cartManager->clearCart($user);
        }
        return $this->redirectToRoute('main_page');
    }

    #[Route(path: '/orders', name: 'app_order_view_orders')]
    public function viewOrders(): Response
    {
        $user = null;
        try {
            $user = $this->userManager->getLoggedInUser($this->getUser());
        } catch (\Exception $e) {
            $this->addFlash('notice', 'You must be logged in to perform this action');
        }
        if ($user) {
            return $this->render('orders.html.twig', [
                'orders' => $this->orderManager->getOrdersForUser($user),
                'order_manager' => $this->orderManager,
            ]);
        }
        return $this->redirectToRoute('main_page');
    }
}