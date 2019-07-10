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
        // On récupère une instance de l'entité pour la lier au formulaire
        $ad = new Ad();

        /**
         * on passe en 1er paramêtre la class du formulaire 
         * et en second l'instance de la nouvelle annonce
         */
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);         

        if($form->isSubmitted() && $form->isValid()){
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
