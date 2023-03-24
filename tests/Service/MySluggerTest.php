<?php

namespace App\Tests\Service;

use App\Services\MySlugger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Dotenv\Dotenv;

class MySluggerTest extends KernelTestCase
{
    public function testMySlugger(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        // TODO : je veux tester mon service MySlugger
        // je récupère mon service
        
        $mySluggerService = static::getContainer()->get(MySlugger::class);
        
        // * 1er cas : un nom de film avec espaces
        $title = "Le nom de film avec des espaces";
        $titleExpected = "Le-nom-de-film-avec-des-espaces";

        /** @var MySlugger $mySluggerService */
        $titleSlug = $mySluggerService->slug($title);

        $this->assertEquals($titleExpected, $titleSlug);
    }


    public function testToLower(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $_ENV["SLUGGER_TO_LOWER"] = "true";
        // dd($_ENV);

        // TODO : je veux tester mon service MySlugger
        // je récupère mon service
        
        $mySluggerService = static::getContainer()->get(MySlugger::class);
        
        // * 1er cas : un nom de film avec espaces
        $title = "Le nom de film avec des espaces";
        $titleExpected = "le-nom-de-film-avec-des-espaces";

        /** @var MySlugger $mySluggerService */
        $titleSlug = $mySluggerService->slug($title);

        $this->assertEquals($titleExpected, $titleSlug);
    }
}
