<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
    private $slugger;

    private $isTolowerActive = false;

    // on est dépendant de SluggerInterface
    // on le demande dès la construction de notre service
    
    /**
    * Constructor
    */
    public function __construct(bool $toLower, SluggerInterface $sluggerInterface)
    {
        $this->slugger = $sluggerInterface;
        
        // dd($toLower);
        $this->isTolowerActive = $toLower;
    }

    /**
     * Génère le slug d'un titre
     *
     * @param string $title
     */
    public function slug($title)
    {
        $slug = $this->slugger->slug($title);

        // ici je peut modifier le comportement du la génération de slug
        // en modifiant la variable $slug
        if ($this->isTolowerActive){
            return $slug->lower();
        }

        return $slug;
    }
}