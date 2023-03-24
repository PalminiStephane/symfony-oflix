<?php

namespace App\Controller;

use App\Repository\CastingRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // ? je vais faire de l'affichage, je crée un dossier 'home' dans les templates

    // TODO : route index : doit afficher la liste des films
    /**
     * affichage de la liste de films
     * route par défaut
     *
     * @Route("/", name="default")
     * @Route("/home", name="app_home_index")
     * 
     * @return Response
     */
    public function index(MovieRepository $movieRepository, GenreRepository $genreRepository): Response
    {
        // TODO j'affiche la liste des genres
        // j'ai besoin d'un repository : GenreRepository
        // le findAll() ne me permet pas de trier les résultats
        // * $allGenre = $genreRepository->findAll();
        $allGenre = $genreRepository->findBy(
            // je n'ai pas de critères, mais je dois fournir un tableau, celui ci sera vide
            [],
            // je veux trier par 'name' et 'ASC' (ordre alphabétique)
            // dans le tableau je fournis: 
            // clé : la propriété 
            // value : DESC ou ASC
            ["name" => "ASC"],
            // les autres paramètres ont des valeur par défaut
            // je ne suis pas obligé de les fournir
        );

        // TODO : je récupère tout les movie de la BDD
        // avec AR : On demande au model un findAll()
        // on est avec DataMapper, on doit demander au repository de la bonne entité
        // * je cherche des Movie, je demande donc à MovieRepository
        // MovieRepository->findAll()
        // ? où est ce que j'obtient MovieRepository ?
        // on doit faire un new MovieRepository(), mais il va nous falloir encore un autre 'truc' demandé dans le contructeur
        // ? symfony, tu peux nous aider ? 
        // * demandons à Symfony de nous fournir MovieRepository
        // de la même manière que nous avons récupérer Request
        // on va faire une injection de dépendance : MovieRepository $movieRepository
        // avec l'injection de dépendance, symfony nous fait le 'new'
        $allMovieFromBDD = $movieRepository->findAll();
        // dump($allMovieFromBDD);
        /*
            0 => App\Entity\Movie {#3396 ▼
                -id: 1
                -title: "Epic Movie"
                -synopsis: "azerater"
                -poster: "https://m.media-amazon.com/images/M/MV5BNThmZGY4NzgtMTM4OC00NzNkLWEwNmEtMjdhMGY5YTc1NDE4XkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg"
                -rating: 5.0
                -duration: 123
                -type: "film"
                -summary: "sdgfh"
                -releaseDate: DateTime @-3600 {#50 ▶}
                -createdAt: DateTime @1676899020 {#3559 ▶}
                -updatedAt: null
            }
        */

        return $this->render("home/index.html.twig", 
        [
            "allMovies" => $allMovieFromBDD,
            "allGenre" => $allGenre
        ]);
    }

    

    // TODO : route show : doit afficher le détails d'un film
    /**
     * affichage des détails d'un film
     *
     * @Route("/movie/{id}/{slug}",name="app_home_show", requirements={"id"="\d+", "slug"="^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$"})
     * @return Response
     */
    public function show(
        $id,
        $slug, 
        MovieRepository $movieRepository,
        CastingRepository $castingRepository,
        ReviewRepository $reviewRepository
        ): Response
    {
        // dump($index);
        // TODO : aller chercher le bon film dans le BDD : MovieRepository->find()
        $movie = $movieRepository->find($id);
        // ? si le slug n'est pas bon on va rediriger vers la bonne URL, pour le référencement
        if ($slug != $movie->getSlug()){
            return $this->redirectToRoute("app_home_show", ["id" => $movie->getId(), "slug" => $movie->getSlug()]);
        }
        // on utilise le findOneBy() en précisant la propriété sur laquelle on cherche, et sa valeur
        // "slug" => $slug
        // $movie = $movieRepository->findOneBy(["slug" => $slug]);
        

        // TODO : si le film n'existe pas je doit renvoyer une 404
        // ! sinon cela va me faire une erreur coté twig
        // dd($movie); // null
        if ($movie === null){
            // renvoyer une 404
            // on lance un exception 404 (notFound)
            // symfony va l'attraper et changer la réponse en réponse 404
            throw $this->createNotFoundException("le film n'existe pas");
        }

        // TODO : aller chercher les castings
        // et ne pas se servir de la relation dans twig
        // Casting : CastingRepository
        $allCastingFromMovie = $castingRepository->findBy(
            // critère : WHERE
            // ! on parle objet
            // on donne le nom de la propriété
            // et la valeur doit être dans ce cas un objet Movie
            [
                "movie" => $movie
            ],
            // ORDER BY
            // ! on parle objet
            [
                "creditOrder" => "ASC"
            ]
        );

        // dd($allCastingFromMovie);

        $allReviews = $reviewRepository->findBy(["movie" => $movie]);

        // ? ici les seasons ne sont pas chargées, 
        // ? il faut faire une boucle (s'en servir) 
        // ? pour Doctrine réagisse et aille les chercher en automatique
        return $this->render("home/show.html.twig", 
        [
            // "movieIndex" => $index, // ne sert plus, relique du temps où on avait pas de BDD
            "movieForView" => $movie,
            "allCasting" => $allCastingFromMovie,
            "reviews" => $allReviews
        ]);
    }

    // TODO : route search : doit afficher un résultat de recherche.
    /**
     * résultat de recherche
     *
     * @Route("/search",name="app_home_search")
     * 
     * @return Response
     */
    public function search(): Response
    {
        return $this->render("home/search.html.twig");
    }

    // ? la route pour les favoris, n'ayant rien à voir avec 'Home'
    // on va le faire dans un autre controller
}