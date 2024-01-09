<?php
namespace App\Controller;

use App\Entity\Order;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
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

    /**
     * @return PayPalHttpClient
     */

    public function getPaypalClient(): PayPalHttpClient
    {
        $clientId = "ASrddIultewYNWzJRCoyUe_rpUz8BaRb3SX9cEU-8UskOqS-JAt0pUlzrmUP7R2b3cK2Z8jf7fSzUmZ5";
        $clientSecret = "ECUwtJpY8aCYePjDuiHcRlzEvgxfIdG1UvaW472v2bzbdMmwOESbnmnYRT6oioV74HrnzmozOFwL2yBS";
        $environment = new SandboxEnvironment($clientId, $clientSecret);
        return new PayPalHttpClient($environment);

    }
    #[Route('/order/payment-stripe/{reference}', name: 'payment_stripe',methods: ['POST'])]
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
            'success_url' => $this->urlGenerator->generate('payment_success_stripe',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('payment_error_stripe',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/order/success/{reference}', name: 'payment_success_stripe')]
    public function stripeSuccess($reference,CartService $cartService): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_index');
        }

        if(!$order->isIsPaid()){
            $cartService->removeCartAll();
            $order->setIsPaid(1);
            $this->em->flush();
        }

        return $this->render('order/success.html.twig',[
            'recapCart' => $cartService->getTotal(),
            'order' => $order,
            'transporter' => $order->getTransporterPrice(),]);
    }

    #[Route('/order/error/{reference}', name: 'payment_error_stripe')]
    public function stripeError($reference,CartService $cartService): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_index');
        }
        return $this->render('order/error.html.twig',[
            'order' => $order,
        ]);
    }

    #[Route('/order/payment-paypal/{reference}', name: 'payment_paypal',methods: ['POST'])]
    public function createSessionPaypal($reference) : RedirectResponse
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            return $this->redirectToRoute('cart_index');
        }
        $items = [];
        $itemTotal = 0;

        foreach ($order->getRecapDetails()->getValues() as $product) {
            $items[] = [
                'name' => $product->getProduct(),
                'quantity' => $product->getQuantity(),
                'unit_amount' => [
                    'currency_code' => 'EUR',
                    'value' => $product->getPrice(),
                ]
            ];
            $itemTotal += $product->getPrice() * $product->getQuantity();
        }
        $total = $itemTotal + $order->getTransporterPrice();
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => $total,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'EUR',
                                'value' => $itemTotal,
                            ],
                            'shipping' => [
                                'currency_code' => 'EUR',
                                'value' => $order->getTransporterPrice(),
                            ],
                        ],
                    ],
                    'items' => $items
                ]
            ],
            'application_context' => [
                'cancel_url' => $this->urlGenerator->generate('payment_error_paypal',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL),
                'return_url' => $this->urlGenerator->generate('payment_success_paypal',['reference' => $order->getReference()],UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];
        $client = $this->getPaypalClient();
        $response = $client->execute($request);
        if($response->statusCode != 201){
            return $this->redirectToRoute('cart_index');
        }
        $approvalLink = '';
        foreach ($response->result->links as $link) {
            if ($link->rel == 'approve') {
                $approvalLink = $link->href;
                break;
            }
        }
        if(empty ($approvalLink)){
            return $this->redirectToRoute('cart_index');
        }
        $order->setPaypalOrderId($response->result->id);

        $this->em->flush();
        return new RedirectResponse($approvalLink);

    }

    #[Route('/order/success-paypal/{reference}', name: 'payment_success_paypal')]
    public function successPaypal($reference,CartService $cartService): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_index');
        }

        if(!$order->isIsPaid()){
            $cartService->removeCartAll();
            $order->setIsPaid(1);
            $this->em->flush();
        }
        return $this->render('order/success.html.twig',[
            'recapCart' => $cartService->getTotal(),
            'order' => $order,
            'transporter' => $order->getTransporterPrice()]);
    }

    #[Route('/order/error-paypal/{reference}', name: 'payment_error_paypal')]
    public function errorPaypal($reference): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_index');
        }
        return $this->render('order/error.html.twig',[
            'order' => $order
        ]);
    }

}