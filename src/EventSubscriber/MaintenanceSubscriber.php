<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    
    private $active;
    
    /**
    * @param bool $active Suivant le paramétrage du fichier .env
    * voir services.yaml
    */
    public function __construct($active)
    {
        $this->active = $active;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->active) {
            // dd($event);
            $contenuHTML = $event->getResponse()->getContent();

            // je veux me placer juste après </nav>
            // et remplacer </nav> par </nav><p>mon texte</p>
            $nouveauContenuHTML = str_replace(
            '</nav>', 
            '</nav><div class="alert alert-danger" onclick="alert(\'coucou\');">Maintenance prévue mercredi 15 mars à 16h00</div>',
            $contenuHTML
            );

            // je modifie le contenu de la réponse 
            $event->getResponse()->setContent($nouveauContenuHTML);
        }
         
         // je n'ai pas besoin de faire de return car la méthode renvoit void
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
