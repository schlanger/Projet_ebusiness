<?php
namespace App\Controller;

use App\Entity\Order;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends  AbstractController {

    public function __construct(EntityManagerInterface $em,UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }
    #[Route('/order/payment-stripe/{reference}', name: 'payment_stripe')]
     public function stripeCheckout($reference): RedirectResponse
    {
        $productStripe = [];

        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        //dd($order);
        //dd($order->getRecapDetails()->getValues());

        if(!$order){
            return $this->redirectToRoute('cart_index');

        }

        foreach($order->getRecapDetails()->getValues() as $product){
            $productStripe [] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        $productStripe [] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getTransporterPrice(),
                'product_data' => [
                    'name' => $order->getTransporterName(),
                ],
            ],
            'quantity' => 1
        ];


        Stripe::setApiKey('sk_test_51O97KZKfnWsxMdxURdo9pzdny0kAYWdgfrVAytMIUQBYo9nBEskzHXCVUWSPcfxFcVpLlg6CTsvu2YJjvLYJMlOw00VCGWnwSS');

        //header('Content-Type: application/json');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                 $productStripe,
            ],
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('payment_success',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('payment_error',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSuccess($reference,CartService $cartService): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/error/{reference}', name: 'payment_error')]
    public function stripeError($reference,CartService $cartService): Response
    {
        return $this->render('order/error.html.twig');
    }

}