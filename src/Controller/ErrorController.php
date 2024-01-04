<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'error_page',methods: ['GET'])]
    public function showError(): Response
    {
        return $this->render('error/404.html.twig', [], new Response('', 404));
    }
}