<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Image;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Utilisation de Faker, localisé en français
        $faker = Factory::create("fr_FR");

        // Nous gérons les utilisateurs
        $users = [];

        // On définit un tableau avec les possibilités de genres
        $genres = ['male', 'female'];

        for( $i = 1; $i <= 10; $i++){
            $user = new User();

            // On sélectionne un genre au hasard
            $genre = $faker->randomElement($genres);

            // On définit la base de l'url de random user
            $picture = "https://randomuser.me/api/portraits/";

            // On sélectione l'id au hasard entre 1 et 99 (c'est le nombre de photos dispos par genre sur random user)
            $pictureId= $faker->numberBetween(1, 99) . '.jpg';

            // On complète l'url selon le genre
            $picture .= ($genre == "male" ? 'men/' : 'women/') . $pictureId;

            // on encoder le mot password avec l'UserPasswordEncoderInterface
            // $hash = $this->encoder->encodePassword($user, 'password');
            $hash = 'password';

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

                 $manager->persist($user);
                 $users[] = $user;
        }

        // Nous gérons les annonces
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);

            // $faker-paragraphs retournant un tableau, on peut utiliser join() pour insérer chaque paragraph dans une balise <p>
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            // On choisit un user au hasard à chaque fois pour pouvoir simuler des users avec plusieurs annonces
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

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
