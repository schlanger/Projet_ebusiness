<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Order;
use App\Entity\RecapDetails;
use App\Form\AdressType;
use App\Form\OrderType;
use App\Service\CartService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/order', name: 'order_index')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        return $this->render('order/index.html.twig', [
            'form' =>$form->createView(),
            'recapCart'=> $cartService->getTotal()
        ]);
    }
    #[Route('/order/enter_delivery_adress', name: 'delivery_adress')]
    public function enterDeliveryAddress(Request $request): Response
    {
        $user = $this->getUser();
         if (!$user) {
            return $this->redirectToRoute('app_login');
        }
         $adress = new Adress();

        $form = $this->createForm(AdressType::class,$adress);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez l'adresse dans la base de données
            $adress->setUser($user);
            $this->entityManager->persist($adress);

            $this->entityManager->flush();

            // Redirigez l'utilisateur vers la page de confirmation de commande
            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/delivery_adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   #[Route('/order/verify', name: 'order_prepare',methods: ['POST'])]
    public function prepareOrder(CartService $cartService,Request $request):Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new \DateTime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('adresses')->getData();
            $deliveryForOrder = $delivery->getFirstname().' '.$delivery->getLastname();
            $deliveryForOrder .= '<br/>'.$delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryForOrder .= '<br/>'.$delivery->getCompany();
            }
            $deliveryForOrder .= '<br/>'.$delivery->getAdress();
            $deliveryForOrder .= '<br/>'.$delivery->getPostalcode().' '.$delivery->getCity();
            $deliveryForOrder .= '<br/>'.$delivery->getCountry();
            //dd($deliveryForOrder);
            $order = new Order();
            $reference = $datetime->format('dmY').'-'.uniqid();
            $order->setUser($this->getUser());
            $order->setReference($reference);
            $order->setCreatedAt($datetime);
            $order->setDelivery($deliveryForOrder);
            $order->setTransporterName($transporter->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setIsPaid(0);
            $order->setMethod('stripe');

            $this->entityManager->persist($order);

            foreach ($cartService->getTotal() as $product)
            {
                //dd($product);
                $recapDetails = new RecapDetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getPrice());
                $recapDetails->setProduct($product['product']->getTitle());
                $recapDetails->setTotalRecap(
                    $product['product']->getPrice() * $product['quantity']);

                $this->entityManager->persist($recapDetails);
            }
            $this->entityManager->flush();
            return $this->render('order/prepare.html.twig',[
                'method' => $order->getMethod(),
                'recapCart' => $cartService->getTotal(),
                'delivery' => $deliveryForOrder,
                'transporter' => $transporter,
                'reference' => $order->getReference(),
            ]);

        }
        return $this->redirectToRoute('cart_index');
   }
}
