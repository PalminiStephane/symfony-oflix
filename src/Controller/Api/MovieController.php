<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends AbstractController
{
    /**
     * @Route("/api/movies", name="app_api_movie", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer, 
        MovieRepository $movieRepository,
        ValidatorInterface $validator
        )
    {
        // TODO : securiser la route : il me faut un user
        $user = $this->getUser();
        
        /* inutile grace à Lexik
        if(!$user){ 
            // depuis une API, pas de redirection, car pas de HTML
            // return $this->redirectToRoute('app_login');
            
            return $this->json("tu n'a pas le droit", Response::HTTP_FORBIDDEN);
        }
        */

        // * ce n'est pas une bonne pratique, on ne veux pas de HTML
        // $this->denyAccessUnlessGranted("ROLE_ADMIN");
        // dd($user);
        if (!$this->isGranted("ROLE_KI_EXISTE_PAS")){
            return $this->json("tu n'a pas le droit", Response::HTTP_FORBIDDEN);
        }

        // TODO : récupérer des infos venant du front
        $contentJson = $request->getContent();

        try {
            // * c'est ici que le denormalizer entre en compte
            $movieFromJson = $serializer->deserialize(
                // 1. le json
                $contentJson,
                // 2. le type, càd la classe Entité
                Movie::class,
                // 3. le format de données
                'json'
                // 4. le contexte, pour l'instant rien à y mettre
            );

        } catch (\Throwable $e){
            // notre exception est dans $e
            // TODO avertir l'utilisateur
            return $this->json(
                // 1. le message d'erreur
                $e->getMessage(),
                // 2. le code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // TODO : on valide les données avant de persist/flush
        // ? https://symfony.com/doc/5.4/validation.html#using-the-validator-service
        $listError = $validator->validate($movieFromJson);

        if (count($listError) > 0){
            // On a des erreurs de validation
            return $this->json(
                // 1. le contenu : la liste des erreurs
                $listError,
                // 2. un code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $movieRepository->add($movieFromJson, true);

        return $this->json(
            // 1. l'objet créé
            $movieFromJson,
            // 2. on change le code pour un 201
            Response::HTTP_CREATED,
            // 3. pas d'entetes particulières
            [],
            // 4. comme on serialize, il faut utiliser les groups
            [
                "groups" => 
                [
                    "movie_read"
                ]
            ]
        );
    }
}
