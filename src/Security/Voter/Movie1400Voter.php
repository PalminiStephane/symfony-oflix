<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class Movie1400Voter extends Voter
{

    /**
     * Est ce que je sais gérer cette règle ?
     *
     * @param string $attribute le nom de la règle
     * @param mixed $subject le contexte de la règle, peut être n'importe quoi, Objet, Tableau ...
     * @return boolean
     */
    protected function supports(string $attribute, $subject): bool
    {
        // Si je suis le ROLE_VOTER de symfony, mon code serait
        // $attribute startwith "ROLE_"

        if ($attribute === "MOVIE1400"){
            return true;
        }

        
        // Si je suis le ROLE_VOTER de symfony, mon code serait
        // $subject instanceof \App\Entity\User;

        return false;
    }

    /**
     * j'appliquema règle
     *
     * @param string $attribute le nom de la règle
     * @param mixed $subject le contexte de la règle, peut être n'importe quoi, Objet, Tableau ...
     * @param TokenInterface $token le système de sécurité, avec lequel on a l'utilisateur: $token->getUser();
     * @return boolean
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $now = new DateTime();
        // G	Heure, au format 24h, sans les zéros initiaux	0 à 23
        // i	Minutes avec les zéros initiaux	00 à 59
        $hour = $now->format("Gi"); // 1320 / 959 / 2359 / 001
        if ($hour > 1400){
            // il est plus de 14h, NON pas le droit
            return false;
        } else {
            // il est moins de 14h00, OUI tu as le droit
            return true;
        }
    }
}
