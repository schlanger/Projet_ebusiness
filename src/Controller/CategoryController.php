<?php

namespace App\Controller;

use App\Entity\CategoryShop;
use App\Repository\CategoryShopRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/boutique/homme', name: 'category_homme', methods: ['GET'])]
    public function ProduitHomme(ProductRepository $productRepository): Response
    {
        // Votre logique de contrôleur pour afficher la catégorie ici

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findBy(['category_id' => 77])
        ]);
    }
    #[Route('/boutique/femme', name: 'category_femme', methods: ['GET'])]
    public function ProduitFemme(ProductRepository $productRepository): Response
    {
        // Votre logique de contrôleur pour afficher la catégorie ici

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findBy(['category_id' => 76])
        ]);
    }
    #[Route('/boutique/enfant', name: 'category_enfant', methods: ['GET'])]
    public function ProduitEnfant(ProductRepository $productRepository): Response
    {
        // Votre logique de contrôleur pour afficher la catégorie ici

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findBy(['category_id' => 72])
        ]);
    }
    #[Route('/boutique/accessoires', name: 'category_accessoires', methods: ['GET'])]
    public function ProduitAccessoires(ProductRepository $productRepository): Response
    {
        // Votre logique de contrôleur pour afficher la catégorie ici

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findBy(['category_id' => 79])
        ]);
    }
}