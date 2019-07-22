<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads/{page<\d+>?1}", name="ads_index")
     */
    public function index(AdRepository $repo, $page, SessionInterface $session)
    {
        // $repo = $this->getDoctrine()->getRepository(Ad::class);
        // $ads = $repo->findAll();

        // On définit le nombre d'annonces par page
        $limit = 6;

        // On calcul le premier article à appeler
        $start = $page * $limit - $limit;

        // On calcul le total d'annonces
        $total = count($repo->findAll());

        // Le nombre de page en arrondissant au supérieur
        $pages = ceil($total / $limit);

        // On renvoit les annonces, le nombre de page et la page actuelle
        return $this->render('ad/index.html.twig', [
            'ads' => $repo->findBy([], [], $limit, $start),
            'pages' => $pages,
            'page' => $page
        ]);
    }


    /**
     * Permet de créer une annonce
     *
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        // On récupère une instance de chaque entité à lier au formulaire
        $ad = new Ad();

        /**
         * on passe en 1er paramêtre la class du formulaire 
         * et en second l'instance de la nouvelle annonce
         */
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);         

        if($form->isSubmitted() && $form->isValid()){

            /*
            Pour chaque image ajoutée à l'entité $ad,
            définir la propriété Ad dans l'entité Image
            et persisté l'entité Image 
            */
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );  

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager){

        /**
         * on passe en 1er paramêtre la class du formulaire 
         * et en second l'instance de la nouvelle annonce
         */
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            /*
            Pour chaque image ajoutée à l'entité $ad,
            définir la propriété Ad dans l'entité Image
            et persisté l'entité Image 
            */
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );  

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }        

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }


    /**
     * Permet d'afficher une seule annonce
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show($slug, AdRepository $repo)
    {
        // Je récupère l'annonce qui correspond au slug
        $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }


    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager )
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            "success",
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("ads_index");
    }
}
