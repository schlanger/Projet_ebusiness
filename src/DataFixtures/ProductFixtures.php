<?php

namespace App\DataFixtures;

use App\Entity\CategoryShop;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $imagePath = 'public/assets/images/women-03.jpg';
        $imagePath2 = 'public/assets/images/baner-right-image-02.jpg';
        $p = [
            1 => [
                'category_id' => 12,
                'title' => 'chapeau',
                'slug' => 'femme',
                'content' => 'super chapeau en feutre noir babacool',
                'online' => '1',
                'subtitle' => 'super qualité 100% fourure de lapin à l intèrieure',
                'price' => '120.99',
                'created_at' => new \DateTime('now')
            ],
            2 => [
                'title' => 'veste',
                'slug' => 'homme',
                'content' => 'super veste noire 100% laine',
                'online' => '1',
                'subtitle' => 'super qualité 100% fourure de lapin à l intèrieure',
                'price' => '120.99',
                'created_at' => new \DateTime('now')

            ]
        ];
        $category = new CategoryShop();
        $category->setName('Femme');
        $category->setSlug('femme');
        $manager->persist($category);
        $manager->flush();
        $category2 = new CategoryShop();
        $category2->setName('Homme');
        $category2->setSlug('men');
        $manager->persist($category2);
        $manager->flush();

        // Référencer la catégorie pour pouvoir y accéder ultérieurement
        $this->addReference('existing-category', $category);
        $this->addReference('product-men', $category2);


        foreach ($p as $k => $value) {
            $uploadedFile = new UploadedFile($imagePath, 'women-03.jpg', 'image/jpeg', null, true);
            $uploadedFile2 = new UploadedFile($imagePath2, 'baner-right-image-02.jpg', 'image/jpeg', null, true);
            $product = new Product();
            $product->setImageFile($uploadedFile);
            $product->setImageFile($uploadedFile2);
            $product->setCategoryId($this->getReference('existing-category'));
            $product->setCategoryId($this->getReference('product-men'));
            $product->setTitle($value['title']);
            $product->setSlug($value['slug']);
            $product->setContent($value['content']);
            $product->setOnline($value['online']);
            $product->setSubtitle($value['subtitle']);
            $product->setPrice($value['price']);
            $product->setCreatedAt($value['created_at']);
            $manager->persist($product);

        }
        $manager->flush();
    }

}