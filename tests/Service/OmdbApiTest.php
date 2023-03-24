<?php

namespace App\Tests\Service;

use App\Services\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OmdbApiTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        
        // on demande au conteneur de service, le service que l'on veut
        // c'est un peu comme si on faisait de l'injection de dépendance
        
        // comme la méthode ->get() me renvoit object, VSCode ne m'aide pas pour les méthode de mon service
        // j'ajoute cette annotations pour lui préciser de quel type est ma variable
        /** @var OmdbApi $omdbApiService */
        $omdbApiService = static::getContainer()->get(OmdbApi::class);

        // ! on s'aperçoit que le paramétrage n'est pas lu dans le .env.local
        // * il faut créer un fichier .env.test.local car on a changé d'environnement
        //dd($omdbApiService);

        // * 1er cas : le cas où ça fonctionne

        $posterUrl = $omdbApiService->getPoster('totoro');
        //dd($posterUrl);

        // je vérifie que j'obtiend bien la même URL en demandant le film 'totoro'
        $this->assertEquals(
            "https://m.media-amazon.com/images/M/MV5BYzJjMTYyMjQtZDI0My00ZjE2LTkyNGYtOTllNGQxNDMyZjE0XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg",
            $posterUrl
        );

        // * 2eme cas : le film n'existe pas
        // je dois recevoir le poster par défaut
        
        $defautlPoster = "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";

        $poster = $omdbApiService->getPoster("film qui n'existe pas");

        $this->assertEquals(
            $defautlPoster,
            $poster
        );
        
        // * 3eme cas : le film qui n'a pas de poster
        // je dois recevoir le poster par défaut
        
        $defautlPoster = "https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg";

        $poster = $omdbApiService->getPoster("Terminator");

        $this->assertEquals(
            $defautlPoster,
            $poster
        );

    }
}
