<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Services\FavorisManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris/{index}", name="app_favoris_index", requirements={"index":"\d+"})
     */
    public function index($index, FavorisManager $favorisManager, Request $request): Response
    {
        // j'utilise le service
        $favorisManager->addOrRemove($index);
        
        // TODO faire une redirection
        // on voudrais rediriger vers l'URL qui nous a amenée ici
        // ? on a t on l'information ? => Request
        // dd($request);
        // dans les entêtes HTTP, il y a l'url "referer" qui est celle d'où on vient
        $referer = $request->headers->get('referer');
        // dd($referer);

        //return $this->redirectToRoute('default');
        // on a juste une URL, on ne peut pas utiliser la méthode redirectToRoute()
        // on va donc construire une réponse nous même : RedirectResponse
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/favoris", name="app_favoris")
     */
    public function browse(FavorisManager $favorisManager, MovieRepository $movieRepository)
    {
        // j'utilise le service
        $listeIdFavoris = $favorisManager->getAll();

        // si l'utilisateur à 200 favoris, on va faire 200 requetes
        // pas ouf au niveau optimisation
        /*
        $movieList = [];
        foreach ($listeIdFavoris as $idMovie) {
            $movieList[] = $movieRepository->find($idMovie);
        }
        */

        // on cherche à obtenir tout les films filtrés par leur ID
        // on utilise donc le findBy() avec un critère sur l'id
        // et on lui donne une liste d'ID
        // cela va générer une requete avec le mot-clé IN
        $movieFavoris = $movieRepository->findBy([
            "id" => $listeIdFavoris
        ]);

        // pour ceux qui utilise le DQL : 
        // SELECT m FROM App\Entity\Movie m WHERE m.id IN (:ids)
        /*
            version avec queryBuilder à retravailler

            $ids = [1, 2, 3];
            $queryBuilder = $entityManager->createQueryBuilder();
            $queryBuilder->select('e')
                ->from('Entity', 'e')
                ->where($queryBuilder->expr()->in('e.id', ':ids'))
                ->setParameter('ids', $ids);
            $result = $queryBuilder->getQuery()->getResult();
        */

        // dd($movieFavoris);

        return $this->render("favoris/browse.html.twig", [
            "favList" => $movieFavoris
        ]); 
    }
}
