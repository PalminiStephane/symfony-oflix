<?php

namespace App\Tests\Front;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreWebTestCase extends WebTestCase
{
    public const USER_MAIL = 'user@user.com';
    public const MANAGER_MAIL = 'manager@manager.com';
    public const ADMIN_MAIL = 'admin@admin.com';


    /**
     * ranvoie un film aléatoire
     *
     * @return Movie
     */
    public function getRandomMovie(): Movie
    {
        /** @var MovieRepository $movieRepository */
        $movieRepository = static::getContainer()->get(MovieRepository::class);
        $allMovie = $movieRepository->findAll();
        
        // je mélange le tableau
        shuffle($allMovie);
        // je renvoit le premier index
        return $allMovie[0];
        
    }

    /**
     * create client and login with email
     *
     * @param string $email 
     * @throws AssertionFailedError if user not found
     * @return KernelBrowser
     */
    public function createClientAuthentificated($email): KernelBrowser
    {
        $client = static::createClient();

        // TODO : pour me logger, il me faut un user, donc BDD, donc UserRepository
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        // on cherche direct l'utilisateur avec le bon role
        $user = $userRepository->findOneBy(['email' => $email]);
        
        if ($user === null)
        {
            $this->fail("create Authenticated client failed : no user found with: ".$email);
        }

        // on se loggue
        $client->loginUser($user);

        return $client;
    }
    /**
    * Override PHPUnit fail method
    * to catch "assertResponse" exceptions
    * 
    * @link https://devdocs.io/phpunit~9/fixtures
    */
    protected function onNotSuccessfulTest(\Throwable $t): void
    {
        // If "assertResponse" is found in the trace, custom message
        if (strpos($t->getTraceAsString(), 'assertResponse') > 0) {
            $arrayMessage = explode("\n", $t->getMessage());
            $message = $arrayMessage[0] . "\n" . $arrayMessage[1];
            $this->fail($message);
        }

        // Other Exceptions
        throw $t;
    }
}