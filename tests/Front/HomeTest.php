<?php

namespace App\Tests\Front;

use App\Repository\MovieRepository;

class HomeTest extends CoreWebTestCase
{
    public function testSomething(): void
    {
        // on créer un client HTTP
        $client = static::createClient();
        // on lance la requete
        // METHOD , URL
        $crawler = $client->request('GET', '/');

        // à partir d'ici on a la réponse, et on peux effectuer des tests dessus

        // je test le code HTTP de retour : 200
        $this->assertResponseIsSuccessful();
        // je regarde dans le contenu de la réponse, si dans une balise <h1> il y a le texte 
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');

    }

    public function testMovieShow()
    {
        // on créer un client HTTP
        $client = static::createClient();
        
        // TODO : il nous faut les information d'un film, donc de la BDD
        // puisque on a utilisé les fixtures
        /** @var MovieRepository $movieRepository */
        $movieRepository = static::getContainer()->get(MovieRepository::class);
        $movieArray = $movieRepository->randomMovie();
        
        // on lance la requete
        // METHOD , URL
        // dd('/movie/' .$movieArray['id']. '/' .$movieArray['title']);
        $crawler = $client->request('GET', '/movie/' .$movieArray['id']. '/' .$movieArray['slug']);

        // ? pour le debug only, mauvaise pratique, on est jamais sûr des ID
        // $movie = $movieRepository->find(12);
        // $crawler = $client->request('GET', '/movie/' .$movie->getId(). '/' .$movie->getSlug());

        // je test le code HTTP de retour : 200
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', $movieArray['title']);

    }
}
