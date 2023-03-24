<?php

namespace App\Tests\Back;

use App\Tests\Front\CoreWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ManagerAccessTest extends CoreWebTestCase
{
    /**
     * Test d'accès Valide pour le Manager
     *
     * @param string $url
     * 
     * @dataProvider getUrlsValid
     */
    public function testManagerValidAccess($url): void
    {
        $client = $this->createClientAuthentificated(CoreWebTestCase::MANAGER_MAIL);
        
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Data provider
     */
    public function getUrlsValid()
    {
        yield ['/backoffice/admin'];
        yield ['/backoffice/season/'];
        yield ['/backoffice/movie/'];
        yield ['/backoffice/user/'];
    }

    /**
     * Test d'accès Valide pour le Manager
     *
     * @param string $url
     * 
     * @dataProvider getUrlsInvalid
     */
    public function testManagerInvalidAccess($url): void
    {
        $client = $this->createClientAuthentificated(CoreWebTestCase::MANAGER_MAIL);
        
        $crawler = $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Data provider
     */
    public function getUrlsInvalid()
    {
        yield ['/backoffice/season/new'];
        yield ['/backoffice/season/1/edit'];
        yield ['/backoffice/movie/new'];
        yield ['/backoffice/movie/1/edit'];
        yield ['/backoffice/user/new'];
        yield ['/backoffice/user/1/edit'];
    }
}
