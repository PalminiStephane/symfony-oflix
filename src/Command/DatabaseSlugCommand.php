<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Services\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseSlugCommand extends Command
{
    /**
     * le nom de la commande
     *
     * @var string
     */
    protected static $defaultName = 'app:database:slug';

    /**
     * description de la commande, disponible quand on utilise le --help
     *
     * @var string
     */
    protected static $defaultDescription = 'Add a short description for your command';

    private $movieRepository;

    private $slugger;

    private $entityManager;

    /**
    * Constructor
    */
    public function __construct(MovieRepository $movieRepository, MySlugger $mySlugger, EntityManagerInterface $entityManagerInterface)
    {
        $this->movieRepository = $movieRepository;
        $this->slugger = $mySlugger;
        $this->entityManager = $entityManagerInterface;

        // ! Command class "App\Command\DatabaseSlugCommand" is not correctly initialized. 
        // ! You probably forgot to call the parent constructor.                                           
        parent::__construct();
    }

    /**
     * c'est ici que l'on déclare les arguments et les options de la commande
     */
    protected function configure(): void
    {
        // TODO si on veux répondre de suite à la question
        // 1. le nom complet de l'option (sans le --)
        // 2. le raccourci de l'opiton, un seul caractère (sans le -)
        // 3. Est ce qu'une valeur est demandé avec l'option
        // on y a indiqué que l'on avait aucune valeur acceptée
        $this->addOption("yesimsure", "y", InputOption::VALUE_NONE, "do not ask me if i'm sure");


        // TODO si je précise l'ID d'un film, je ne traite que ce film
        // c'est une précision de contexte, on ajoute un argument
        // 1. le nom de l'argurment
        // 2. si il est requis pour l'éxécution de la commande
        $this->addArgument("idmovie", InputArgument::OPTIONAL, "l'identifiant du film que l'on veux traiter");
    }

    /**
     * C'est dans cette fonction que l'on va coder notre commande, ce qui va être éxecuté
     *
     * @param InputInterface $input la classe qui s'occupe des informtion en entrée / saisies
     * @param OutputInterface $output la classe qui s'occupe de l'écriture dans le terminal
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ici on instancie une classe pour avoir des outils pour manipuler le terminal
        $io = new SymfonyStyle($input, $output);
        $io->title("Les Limaces dominent le monde !");

        // est ce que l'on désactive la question
        // le fait que cela soit un VALUE_NONE, la valeur par défaut est : false
        // si on utilise l'option, la valeur sera : true
        if (!$input->getOption('yesimsure')){
            // ici on a pas désactivé la confirmation par l'option
            $continue = $io->confirm("Etes vous sûr ?");
            if (!$continue) {
                // l'utilisateur a répondu non
                $io->warning("Aucun slug n'a été généré");
                return Command::INVALID;
            }
        }

        // TODO : si on me fournit l'ID d'un film, je ne traite que ce film
        $idmovie = $input->getArgument('idmovie');
        if ($idmovie)
        {
            // si on m'a fournit l'argument
            $movie = $this->movieRepository->find($idmovie);
            $io->note("génération du slug du film : " . $movie->getTitle());
            // calculer le slug : il nous faut MySlugger
            $slugtitle = $this->slugger->slug($movie->getTitle());
            // mettre à jour l'objet $movie
            $movie->setSlug($slugtitle);

        } else {

            // ici on ne m'a pas fournit d'ID, je traite tout les films
            // TODO : mon objectif est de générer les slugs de tout les films
            // ? j'ai besoin de la BDD, donc d'un repository, MovieRepository
            $allMovies = $this->movieRepository->findAll();
            foreach ($allMovies as $movie) {
                
                $io->note("génération du slug du film : " . $movie->getTitle());
                // calculer le slug : il nous faut MySlugger
                $slugtitle = $this->slugger->slug($movie->getTitle());
                // mettre à jour l'objet $movie
                $movie->setSlug($slugtitle);

            }
        }

        
        
        //TODO : faire le flush() pour enregistrer toutes les modifications de nos entités
        // ? il nous faut l'EntityManager
        $this->entityManager->flush();

        $io->success('Les slugs ont été générés.');

        return Command::SUCCESS;

    }
}
