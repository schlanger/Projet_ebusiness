<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'card_index',methods: ['GET'])]
    public function index(SessionInterface $session,ProductRepository $productRepository): Response
    {
        //dd($cartService->getTotal());
        //dd($cartService->addToCart(1));
        $panier = $session->get('panier',[]);
        //dd($panier);
        $data = [];
        $total = 0;
        foreach ($panier as $id => $quantity){
            $product = $productRepository->find($id);
            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        $session->set('panier',[]);
        //dd($data);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'data' => $data,
            'total' => $total
        ]);
    }
    #[Route('/add/{id}', name: 'card_add')]
    public function add(Product $product,SessionInterface $session)
    {
         $id = $product->getId();

         $panier = $session->get('panier',[]);
            if(empty($panier[$id])) {
                $panier[$id] = 1;
            }else{
                $panier[$id]++;
            }
             $session->set('panier',$panier);
            //dd($session);
        return $this->redirectToRoute('card_index');

    }
    #[Route('/remove/{id}', name: 'card_remove')]
    public function remove(Product $product,SessionInterface $session)
    {
        $id = $product->getId();

        $panier = $session->get('panier',[]);
        if(!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
                $panier[$id] = 1;
            } else {
                unset($panier[$id]);
            }
        }
        $session->set('panier',$panier);
        //dd($session);
        return $this->redirectToRoute('card_index');

    }
    #[Route('/delete/{id}', name: 'card_delete')]
    public function delete(Product $product,SessionInterface $session)
    {
        $id = $product->getId();

        $panier = $session->get('panier',[]);
        if(!empty($panier[$id])) {
                unset($panier[$id]);
        }
        $session->set('panier',$panier);
        //dd($session);
        return $this->redirectToRoute('card_index');

    }
    #[Route('/empty', name: 'card_empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('panier');
        return $this->redirectToRoute('card_index');
    }
}
