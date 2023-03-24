<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Form\GenreType;

class GenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="app_api_genre", methods={"GET"})
     * 
     *  @OA\Response(
     *     response=200,
     *     description="Returns all the genres",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Genre::class, groups={"genre_browse"}))
     *     )
     * )
     */
    public function browse(GenreRepository $genreRepository): JsonResponse
    {
        // TODO : renvoyer tout les genres
        // BDD, injection repository
        $allGenre = $genreRepository->findAll();
        
        // on ne fait pas de render, car on utilise pas twig
        // notre objectif sera de fournir du JSON
        return $this->json(
            // le premier paramètre est les données à transmettre : des objets, des tableaux ...
            $allGenre,
            // comme on veut le 4eme paramètre, on est obligé de fournir le 2e et 3e
            Response::HTTP_OK,
            // * entête HTTP : content,
            // puisque on utilise la méthode json(), 
            // l'entête ["content" => "application/json"] sera automatiquement ajoutée
            [],
            // 4e paramètre : les groups
            //! si je ne précise pas les groups, la méthode va chercher toutes les propriétés, et donc tourner en rond
            [
                "groups" => 
                [
                    "genre_browse"
                ]
            ]
        );
    }
    
    /**
     * @Route("/api/genres", name="app_api_genre_add", methods={"POST"})
     * 
     * @OA\RequestBody(
     *     @Model(type=GenreType::class)
     * )
     * 
     * @OA\Response(
     *     response=201,
     *     description="new created genre",
     *     @OA\JsonContent(
     *          ref=@Model(type=Genre::class, groups={"genre_read", "movie_read"})
     *      )
     * )
     * 
     * @OA\Response(
     *     response=422,
     *     description="NotEncodableValueException"
     * )
     */
    public function add(
        Request $request,
        SerializerInterface $serializer, 
        GenreRepository $genreRepository,
        ValidatorInterface $validator
        )
    {
        // TODO : récupérer des infos venant du front
        // notre protocole de communication est JSON
        // ? où est le JSON que va nous envoyer le front : dans la requête : Request
        $contentJson = $request->getContent();

        // PHP nous propose une méthode pour transformer du JSON en variable PHP : json_decode()
        // ce n'est pas suffisant pour nous, on veut une Entité
        // Symfony à un service pour ça : SerializerInterface
        
        // ! Control character error, possibly incorrectly encoded
        try {
            $genreFromJson = $serializer->deserialize(
                // 1. le json
                $contentJson,
                // 2. le type, càd la classe Entité
                Genre::class,
                // 3. le format de données
                'json'
                // 4. le contexte, pour l'instant rien à y mettre
            );
            // je précise un type d'erreur en précisant la classe dans le catch
            // ici j'attrape litéralement tout ce qui se 'lance', càd tout les types d'erreurs
            // j'aurais pu préciser NotEncodableValueException
        // * } catch (NotEncodableValueException $e){
            // ici on aura que les erreurs de type NotEncodableValueException
        } catch (\Throwable $e){
            // notre exception est dans $e
            // dd($e);
            // TODO avertir l'utilisateur
            return $this->json(
                // 1. le message d'erreur
                $e->getMessage(),
                // 2. le code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY

            );
        }
        
        // on débug : c'est une Entité
        // dd($genreFromJson);

        // TODO : on valide les données avant de persist/flush
        // ? https://symfony.com/doc/5.4/validation.html#using-the-validator-service
        $listError = $validator->validate($genreFromJson);

        if (count($listError) > 0){
            // On a des erreurs de validation
            return $this->json(
                // 1. le contenu : la liste des erreurs
                $listError,
                // 2. un code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // il ne reste plus qu'a faire persist + flush
        // merci baptiste
        $genreRepository->add($genreFromJson, true);
        // dd($genreFromJson);
        
        // il faut tenir au courant notre utilisateur
        // le code http approprié : 201 => Response::HTTP_CREATED
        return $this->json(
            // 1. l'objet créé
            $genreFromJson,
            // 2. on change le code pour un 201
            Response::HTTP_CREATED,
            // 3. pas d'entetes particulières
            [],
            // 4. comme on serialize, il faut utiliser les groups
            [
                "groups" => 
                [
                    "genre_read",
                    "movie_read"
                ]
            ]
        );
    }

    /**
     * @Route("/api/genres/{id}", name="app_api_genre_edit", methods={"PUT", "PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(
        Genre $genre = null, 
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
        )
    {
        // TODO : on modifie une entité
        // 1. l'entité à modifier : paramètre de route
        if ($genre === null){
            // le paramConverter n'a pas trouvé l'entité : 404
            return $this->json("Genre non trouvé", Response::HTTP_NOT_FOUND);
        }
        // 2. les informations de la requete
        $jsonContent = $request->getContent();

        // 3. je déserialize
        try {
            $serializer->deserialize(
                $jsonContent,
                Genre::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $genre]
            );
        } catch (\Throwable $e){
            // notre exception est dans $e
            // dd($e);
            // TODO avertir l'utilisateur
            return $this->json(
                // 1. le message d'erreur
                $e->getMessage(),
                // 2. le code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY

            );
        }
        // il faut faire l'association entre TOUTE les propriétés
        // là encore ça va 1 prop, mais avec Movie :'(
        // $genre->setName($genreUpdate->getName());
        // * on utilise donc une option du serializer pour qu'il nous mettes à jour notre entité
        // ? https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        // un peu comme le handleRequest d'un formulaire

        // TODO : on valide les données avant de persist/flush
        // ? https://symfony.com/doc/5.4/validation.html#using-the-validator-service
        $listError = $validator->validate($genre);

        if (count($listError) > 0){
            // On a des erreurs de validation
            return $this->json(
                // 1. le contenu : la liste des erreurs
                $listError,
                // 2. un code approprié : 422
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // * ici mon objet $genre a été modifié
        // un flush est tout est bon
        $entityManager->flush();
        
        // TODO : return json
        return $this->json(
            // aucune donnée à renvoyer, puisque c'est juste une mise à jour
            // à voir avec votre dev front
            // ! si le code 204 est utilisé aucune donnée ne sera envoyé
            null,
            // le code approprié : 204
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/api/genres/{id}", name="app_api_genre_read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(Genre $genre = null)
    {
        // notre utilisateur a fournit un mauvais ID, je lui donne une 404
        if ($genre === null){
            // on a pas trouvé le genre
            // ! on ne fait pas d'exception, car les execption symfony sont en HTML
            // * en mode API, on a pas d'HTML
            return $this->json(
                // les données, comme c'est une erreur, on met juste un message
                [
                    "message" => "ce genre n'existe pas"
                ], 
                // on doit préciser la 404
                Response::HTTP_NOT_FOUND
                // pas d'entête particulières
                // pas d'option non plus
            );
        }

        return $this->json(
            $genre,
            Response::HTTP_FOUND,
            [],
            [
                "groups" => 
                [
                    "genre_read",
                    "movie_read"
                ]
            ]
        );

    }


    /**
     * @Route("/api/genres/{id}", name="app_api_genre_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Genre $genre = null, GenreRepository $genreRepository)
    {
        // 1. l'entité à supprimer : paramètre de route
        if ($genre === null){
            // le paramConverter n'a pas trouvé l'entité : 404
            return $this->json("Genre non trouvé", Response::HTTP_NOT_FOUND);
        }

        // pas de lecture de JSON
        // pas de validation de données
        // on supprime
        $genreRepository->remove($genre, true);

        // on renvoit quand même un code
        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );

    }


    /**
     * @Route("/api/genres/{id}/movies", name="app_api_movies_by_genre", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function moviesByGenre()
    {

    }





    /**
     * ! Route("/api/genres/{id}/movies/{movieid}", name="app_api_movie_by_genre", requirements={"id"="\d+"})
     * @Route("/api/movies/{id}", name="app_api_movie_by_genre", requirements={"id"="\d+"})
     */
    /*
     public function read()
    {

    }
    */
}
