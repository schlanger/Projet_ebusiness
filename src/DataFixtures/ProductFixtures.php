<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*
        $faker = Faker\Factory ::create('fr_FR');
        for ($i = 0; $i < 50; $i++ ) {
            $category = $this->getReference('categoryShop' . $faker->numberBetween(1,4));
            $product = new Product();
            $product->setTitle($faker->sentence);
            $product->setSlug($faker->slug);
            $product->setContent($faker->text);
            $product->setOnline(true);
            $product->setCreatedAt(new \DateTime('now'));
            $product->setSubtitle($faker->text(155));
            $product->setAttachement($faker->imageUrl(640,480,'animals',true));
            $product->setPrice($faker->randomFloat(2));
            $product->setCategoryId($category);
            $manager->persist($product);

        }*/
        // $product = new Product();
        $p = [
            1 => [
                'category_id' => '12',
                'title' => 'chapeau',
                'slug' => '/femme',
                'content'=> 'super chapeau en feutre noir babacool',
                'online'=> '1',
                'subtitle' => 'super qualité 100% fourure de lapin à l intèrieure',
                'price' => '120.99',
                'created_at' => '2023-12-09 18:58:00',
                'attachement'
            ]
            ]


        $manager->flush();
    }

}