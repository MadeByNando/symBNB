<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Image;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Utilisation de Faker, localisé en français
        $faker = Factory::create("fr-FR");

        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);

            // $faker-paragraphs retournant un tableau, on peut utiliser join() pour insérer chaque paragraph dans une balise <p>
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5));

            // Pour les autres images
            for ($j = 0; $j <= mt_rand(2, 5); $j++) { 
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad); // Ici on définit le paramêtre Ad avec l'instance Ad()

                $manager->persist($image);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
