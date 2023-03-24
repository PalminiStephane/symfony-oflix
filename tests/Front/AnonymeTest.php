<?php

namespace App\Tests\Front;

use App\Repository\MovieRepository;

class AnonymeTest extends CoreWebTestCase
{
    
    /**
     * test anonyme
     *
     * @param string $url
     * 
     * @dataProvider getUrls
     */
    public function testAnonymeAddReview($url): void
    {
        // on créer le client
        $client = static::createClient();
        
        // on fait une requete direct vers une page interdite
        $crawler = $client->request('GET', $url);

        $this->assertResponseRedirects('/login');
    }

    public function getUrls()
    {
        // ? https://www.php.net/manual/fr/language.generators.syntax.php#control-structures.yield
        yield ['/review/1']; // la valeur au 1er appel
        yield ['/backoffice/admin']; // la valeur au 2eme appel
        yield ['/backoffice/season/']; // la valeur au 3eme appel

    }
}
