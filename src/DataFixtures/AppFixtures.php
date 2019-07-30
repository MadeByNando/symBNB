<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
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

        // On crée un role admin
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // On crée un utilisateur admin
        $adminUser = new User();
        $adminUser->setFirstName("Fernando")
                  ->setLastName("Pinho")
                  ->setEmail("pinho.dcj@gmail.com")
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture("https://secure.gravatar.com/avatar/52256d662cd2a8e6145df63cc663e74b")
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);

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
            $hash = $this->encoder->encodePassword($user, 'password');

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
            $random_ad_user = mt_rand(0, count($users) - 1);
            $user = $users[$random_ad_user];

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

            // Gestion des réservations
            for ($k = 1; $k <= mt_rand(0,10); $k++) { 
                $booking = new Booking();

                // Réservation effectuée entre il y a 6 mois et 3 mois 
                $createdAt = $faker->dateTimeBetween('-6 months', '-3 months');

                // Date d'arrivée dans le logement entre il y a 3 mois et maintenant 
                // (pour que la date d'arrivée soit toujours après la date de création de la réservation)
                $startDate = $faker->dateTimeBetween('-3 months');

                // Durée entre 3 et 10 jours
                $duration = mt_rand(3, 10);

                // Pour la date de fin, on clone la date de début pour ne pas la modifier, et on y ajoute la durée de la réservation
                $endDate = (clone $startDate)->modify("+$duration days");

                // Prix de la réservation 
                $amount = $ad->getPrice() * $duration;

                // Utilisateur qui a effectué la réservation (en excluant l'utilisateur qui a posté l'annonce)
                do {
                    $random_booking_user = mt_rand(0, count($users) -1);
                } while ($random_booking_user == $random_ad_user);
                $booker = $users[$random_booking_user];

                // On ajoute un commentaire
                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);
                        
                $manager->persist($booking);

            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
