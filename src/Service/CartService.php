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


    public function getTotal(): array {
        $cart = $this->getSession()->get('cart');
        $cartData = [];

        foreach ((array)$cart as $id => $quantity) {
            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            } else {
                echo "Aucun produit n'est présent en base";
                // Vous pouvez ajouter des logs ou des messages ici pour déboguer
                // si un produit n'est pas trouvé dans la base de données.
            }
        }

        return $cartData;
    }

    private function getSession() :SessionInterface {
        return $this->requestStack->getSession();
    }
}
