<?php

namespace App\Tests\Front;

use Faker\Factory;

class AddReviewTest extends CoreWebTestCase
{
    public function testAddReviewWithUser(): void
    {
        // je profite d'avoir une classe parente pour y déposer des méthode utilitaires
        // comme je répète les mail régulièrement, je vais les stocker dans la classe parente
        // je n'ai pas envie qu'il soit modifiable, je les mets en constante
        $client = $this->createClientAuthentificated(CoreWebTestCase::USER_MAIL);

        // pour créer un review, il me faut un id de film
        $movie = $this->getRandomMovie();
        $crawler = $client->request('GET', '/review/' . $movie->getId());

        $this->assertResponseIsSuccessful();

        // on peut utiliser le faker pour les données
        $faker = Factory::create('fr_FR');

        $crawler = $client->submitForm('Ajouter la critique', [
            'review[username]' => $faker->name(),
            'review[email]' => $faker->email(),
            'review[content]' => $faker->paragraph(),
            'review[rating]' => $faker->numberBetween(1,5),
            'review[reactions]' => ['smile','cry'],
            'review[watchedAt]' => $faker->date()
        ]);

        $this->assertResponseRedirects('/movie/' . $movie->getId() . '/' . $movie->getSlug());

    }
}
