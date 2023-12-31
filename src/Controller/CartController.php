<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/', name: 'cart_index',methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        //dd($cartService->getTotal());
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(),
        ]);
    }
    #[Route('/add/{id<\d+>}', name: 'cart_add',methods: ['GET','POST'])]
    public function add(CartService $cartService,int $id,Product $product, SessionInterface $session)
    {
        //$session->start();

        //dd($panier);
        $cartService->addToCart($id);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove', name: 'cart_remove')]
    public function remove(CartService $cartService):Response
    {
        $cartService->removeCartAll();

        return $this->redirectToRoute('app_product');
    }

    #[Route('/delete/{id}', name: 'cart_delete')]
    public function delete(CartService $cartService,int $id):Response
    {
        $cartService->removeToCart($id);

        return $this->redirectToRoute('cart_index');

    }

    #[Route('/decrease/{id}', name: 'cart_decrease')]
    public function decrease(CartService $cartService,int $id):Response
    {
        $cartService->decrease($id);

        return $this->redirectToRoute('cart_index');
    }
    #[Route('/empty', name: 'card_empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('panier');
        return $this->redirectToRoute('cart_index');
    }

    private function getSession() :SessionInterface {
        return $this->requestStack->getSession();
    }
}
