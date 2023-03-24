<?php

namespace App\Controller\Backoffice;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Services\MySlugger;
use App\Services\OmdbApi;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// annotation @IsGranted
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * 
 * cette annotation va lancer une exception 403
 * @IsGranted("ROLE_MANAGER", message="No access! Get out!")
 * 
 * @Route("/backoffice/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="app_backoffice_movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('backoffice/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_movie_new", methods={"GET", "POST"})
     * 
     * cette annotation va lancer une exception 403
     * @IsGranted("ROLE_ADMIN", message="No access! Get out!")
     */
    public function new(Request $request, MovieRepository $movieRepository, OmdbApi $api, MySlugger $mySlugger): Response
    {
        // cela lance une exception, qui arrête notre code immédiatement
        // cette ligne est strictement identique au fonctionnement du security.yaml
        // * $this->denyAccessUnlessGranted("ROLE_ADMIN");
        // à partir d'ici, mon utilisateur à le ROLE_ADMIN

        if (!$this->isGranted("ROLE_ADMIN"))
        {
            // ici mon utilisateur n'a pas le ROLE_ADMIN
            // je redirige mon utilisateur
            return $this->redirectToRoute("app_backoffice_movie_index");
        }

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on met à jour le poster via notre service API
            $poster = $api->getPoster($movie->getTitle());
            // puisuqe on s'occupe du poster, on le désactive du formulaire
            $movie->setPoster($poster);

            // mettre à jour le slug depuis le titre fournit
            // il me faut le SluggerInterface
            $slugTitle = $mySlugger->slug($movie->getTitle());
            $movie->setSlug($slugTitle);

            $movieRepository->add($movie, true);

            return $this->redirectToRoute('app_backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_movie_show", methods={"GET"})
     */
    public function show(Movie $movie): Response
    {
        return $this->render('backoffice/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_backoffice_movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie, OmdbApi $api, EntityManagerInterface $em, MySlugger $mySlugger): Response
    {
        
        // TODO : le film avec l'ID 301 ne doit pas être modifiable : accesDenied
        if ($movie->getId() === 301)
        {
            throw $this->createAccessDeniedException("Non pas le film 301");
        }        
        //  if ($titre== "la septieme compagnie" ) {return $this->redirectoroute('gé-glissé_chef'); }
         
        if (!$this->isGranted("MOVIE301", $movie))
        {
            throw $this->createAccessDeniedException("Non pas le film 301");
        }

        // permet de checker si le nom a été modifié
        $oldTitle = $movie->getTitle();


        $form = $this->createForm(MovieType::class, $movie);
        // le title va être mis à jour si il a changé
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($oldTitle !== $movie->getTitle()) {
                
                $movie->setPoster($api->getPoster($movie->getTitle()));
                // mettre à jour le slug depuis le titre fournit
                // il me faut le SluggerInterface
                $slugTitle = $mySlugger->slug($movie->getTitle());
                //dd($slugTitle);
                $movie->setSlug($slugTitle);

            }

            // * géré par l'event PreUpdate dans l'entité
            // $movie->setUpdatedAt(new DateTime());

            $em->flush();
            // dd($movie);
            // $movieRepository->add($movie, true);

            return $this->redirectToRoute('app_backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $movieRepository->remove($movie, true);
        }

        return $this->redirectToRoute('app_backoffice_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}
