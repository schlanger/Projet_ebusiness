<?php
namespace App\Service;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(RequestStack $requestStack,EntityManagerInterface $entityManager)
    {
     $this->requestStack = $requestStack;
     $this->entityManager = $entityManager;
    }
    public function addToCart(int $id):void {
       $card =  $this->requestStack->getSession()->get('cart',[]);

       if(!empty($card[$id])){
           $card[$id]++;
       }else{
           $card[$id] = 1;
       }

       $this->getSession()->set('cart',$card);
    }

    public function removeToCart(int $id) {
        $cart =  $this->requestStack->getSession()->get('cart',[]);
        unset($cart[$id]);
        return $this->getSession()->set('cart',$cart);
    }

    public function decrease(int $id)
    {
        $cart = $this->getSession()->get('cart',[]);
        if($cart[$id] > 1){
            $cart[$id]--;

    }else {
            unset($cart[$id]);
        }
        $this->getSession()->set('cart',$cart);
    }
    public function removeCartAll()
    {
        return $this->getSession()->remove('cart');
    }


    public function getTotal(): array {
        $cart = $this->getSession()->get('cart');
        $cartData = [];
        if($cart) {
            foreach ($cart as $id => $quantity) {
                $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

                if (!$product) {
                    $this->removeToCart($id);
                    continue;
                }
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartData;
    }

    private function getSession() :SessionInterface {
        return $this->requestStack->getSession();
    }
}
