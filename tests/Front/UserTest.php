<?php

namespace App\Tests\Front;

use App\Repository\UserRepository;

class UserTest extends CoreWebTestCase
{
    public function testUserReview(): void
    {
        $client = $this->createClientAuthentificated(CoreWebTestCase::USER_MAIL);
        
        $crawler = $client->request('GET', '/review/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Ajouter une critique sur le film');
    }
}
