<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{

    /**
     * @Route("/api/genre/demo", name="app_api_genre_demo")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Api/GenreController.php',
        ]);
    }

    /**
     * ma page de demo
     * 
     * @Route("/demo", name="app_demo_index")
     */
    public function nainPorteQuoi(): Response
    {
        // le premier paramètre est le chemin de la vue, à partir du dossier template
        // * on a pour habitude, de ranger les templates dans des dossiers nommés comme le controller qui les utilise
        // le deuxième paramètre est un tableau avec les données que l'on veux afficher
        // par exemple, une liste de films
        return $this->render("demo/nainPorteQuoi.html.twig");
    }

    /**
     * @Route("/demo/seasons", name="demo_seasons")
     */
    public function seasonWithBadPropertyName(SeasonRepository $seasonRepository)
    {
        // TODO récup toute les saisons : SeasonRepository + findAll()
        
        return $this->render("demo/seasons.html.twig", [
            "seasons" => $seasonRepository->findAll()
        ]);

        return new JsonResponse();
    }
}