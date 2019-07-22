<?php  

namespace App\Controller;

use App\Entity\Ad;
use App\Service\Utilities;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * @Route("/hello/{prenom}/age/{age}", name="hello")
     * @Route("/hello", name= "hello_base")
     * @Route("/hello/{prenom}", name= "hello_prenom")
     * Montre la page qui dit bonjour
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0){
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age
            ]
        );
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function home( Utilities $utils ){

        $sayHello = $utils->sayHello('Jean');

        $prenoms = [
            "Fernando" => 31, 
            "Jean" => 12, 
            "Anne" => 55
        ];

        return $this->render(
            'home.html.twig',
            [
                'title' => "Bonjour à tous",
                'sayHello' => $sayHello,
                "age" => 31,
                "tableau" => $prenoms
            ]
        );
    }

    /**
     * @Route("/test/{id}", name="test")
     *
     * @return Response
     */
    public function test(Ad $ad)
    {

        return $this->render("test.html.twig", [
            "ad" => $ad
        ]);
    }

}

?>