<?php

namespace App\DataFixtures;

use App\Entity\CategoryShop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class CategorieShopFixtures extends Fixture
{
public function load(ObjectManager $manager): void
{
    $c = [
        1=> [
            'name' => 'Femme',
            'slug' => 'femme'
        ],
        2 => [
            'name' => 'enfant',
            'slug' => 'enfant'
        ],
        3=> [
            'name' => 'sac à dos ',
            'slug' => 'sacados'
        ],
        4 => [
            'name' => 'Bonnet',
            'slug' => 'bonnet'
        ],
        5 => [
            'name' => 'homme',
            'slug' => 'men'
        ]
    ];
    foreach ($c as $k => $value){
        $categoryshop = new CategoryShop();
        $categoryshop->setName($value['name']);
        $categoryshop->setSlug($value['slug']);
        $manager->persist($categoryshop);
        $this->addReference('categoryShop' .$k, $categoryshop);
    }
// $product = new Product();
// $manager->persist($product);

$manager->flush();
}
}
