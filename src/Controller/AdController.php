<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\AnnonceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Image;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo, SessionInterface $session)
    {
        // $repo = $this->getDoctrine()->getRepository(Ad::class);

        dump($session);

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


    /**
     * Permet de créer une annonce
     *
     * @Route("/ads/new", name="ads_create")
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
}
