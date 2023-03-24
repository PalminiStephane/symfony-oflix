<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi
{
    // ? https://www.omdbapi.com/
    
    // eg: https://www.omdbapi.com/?apikey=a93b767b&t=totoro

    // TODO : on besoin de se connecter à OMDBAPI
    // et faire une requête à l'API en GET, avec des paramètres
    // https://www.omdbapi.com/?apikey={API_KEY}&t={MOVIE_NAME}

    // pour 'parler' à l'API, il me faut un client HTTP
    // ? https://symfony.com/doc/current/http_client.html
    // HttpClientInterface va nous permettre de faire des requetes HTTP
    // mon service est dépendant de cet autre service
    // je le demande à la construction

    // ! la valeur par defaut ne servira jamais, car écrasée dans le constructeur
    private $apiKey = "a93b767b";

    private $defaultPoster = "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";

    private $client;

    /**
    * Constructor
    * @param string $apiKey argument fournit par le services.yaml
    * @param parameterBagInter $parameterBag permet de lire les paramètre du services.yaml
    */
    public function __construct(HttpClientInterface $client, $apiKey, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        // je peux maintenant lire tout les paramètres du services.yaml
        // $this->apiKey = $parameterBag->get('app.omdbapi_apikey');
        
        // je récupère l'argument donné automatiquement par le services.yaml
        $this->apiKey = $apiKey;
    }

    /**
     * récupère toutes les informations depuis l'API en cherchant avec le titre
     *
     * @param string $movieTitle
     */
    protected function fetch($movieTitle)
    {
        $url = "https://www.omdbapi.com/?apikey=".$this->apiKey."&t=".$movieTitle;

        $response = $this->client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        // dump($content);
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
        // dd($content);

        return $content;
    }

    public function getPoster($movieTitle)
    {
        $fullContent = $this->fetch($movieTitle);

        //! ne jamais utiliser une clé de tableau sans avoir tester son existence avant
        /*
        https://www.omdbapi.com/?apikey=a93b767b&t=aaaaaaaaaaaaaaaaaaaaaaaaaaa
        {
            "Response": "False",
            "Error": "Movie not found!"
        }
        */
        if(array_key_exists("Poster", $fullContent)){
            /*  Merci Angèle
                https://www.omdbapi.com/?apikey=a93b767b&t=Terminator
                "Poster": "N/A",
            */
            if ($fullContent['Poster'] === "N/A"){
                // pour la demo de test fail
                // return $fullContent['Poster'];
                return $this->defaultPoster;
            }
            return $fullContent['Poster'];
        } else {
            return $this->defaultPoster;
        }
    }
}