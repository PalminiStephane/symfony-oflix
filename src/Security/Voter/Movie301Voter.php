<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class Movie301Voter extends Voter
{

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return $attribute === "MOVIE301"
            && $subject instanceof \App\Entity\Movie;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array($subject->getId(), range(30,350)))
        {
            return false;
        } else {
            return true;
        }

        if ($subject->getId() === 301)
        {
            return false;
        } else {
            return true;
        }
        
    }
}
