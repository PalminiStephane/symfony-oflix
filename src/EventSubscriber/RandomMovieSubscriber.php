<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $movieRepository;
    private $twig;
    
    /**
    * Constructor
    */
    public function __construct(MovieRepository $movieRepository, Environment $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // dd($event);
        /*
            RandomMovieSubscriber.php on line 12:
            Symfony\Component\HttpKernel\Event\ControllerEvent {#1372 ▼
            -controller: array:2 [▼
                0 => App\Controller\HomeController {#1294 ▶}
                1 => "index"
            ]
            -kernel: Symfony\Component\HttpKernel\HttpKernel {#3434 ▶}
            -request: Symfony\Component\HttpFoundation\Request {#2 ▼
                +attributes: Symfony\Component\HttpFoundation\ParameterBag {#14 ▶}
                +request: Symfony\Component\HttpFoundation\InputBag {#10 ▶}
                +query: Symfony\Component\HttpFoundation\InputBag {#9 ▶}
                +server: Symfony\Component\HttpFoundation\ServerBag {#17 ▶}
                +files: Symfony\Component\HttpFoundation\FileBag {#16 ▶}
                +cookies: Symfony\Component\HttpFoundation\InputBag {#15 ▶}
                +headers: Symfony\Component\HttpFoundation\HeaderBag {#18 ▶}
                #content: null
                #languages: null
                #charsets: null
                #encodings: null
                #acceptableContentTypes: null
                #pathInfo: "/"
                #requestUri: "/"
                #baseUrl: ""
                #basePath: null
                #method: "GET"
                #format: null
                #session: Closure() {#5085 ▶}
                #locale: null
                #defaultLocale: "en"
                -preferredFormat: null
                -isHostValid: true
                -isForwardedValid: true
                -isSafeContentPreferred: null
                basePath: ""
                format: "html"
            }
            -requestType: 1
            -propagationStopped: false
            }
        */

        $pathInfo = $event->getRequest()->getPathInfo();
        if (substr($pathInfo, 0, 5) !== "/back"){
            // TODO : je veux un film aléatoire
            // la BDD, MovieRepository, injection de dépendance, constructeur
            // une valeur en dur pour tester : 662
            // $randomMovie = $this->movieRepository->find(662);
            $randomMovie = $this->movieRepository->randomMovie();
            
            // TODO : je veux donner ce film à twig
            // injection de dépendance de Twig : Environment
            $this->twig->addGlobal("randomMovie", $randomMovie);
        
        }

        // pas de return car la méthode renvoit void

    }

    public static function getSubscribedEvents(): array
    {
        return [
             // on associe l'evènement kernel.controller à la méthode que l'on va vouloir éxécuter sur cet évènement
            'kernel.controller' => 'onKernelController',
        ];
    }
}
