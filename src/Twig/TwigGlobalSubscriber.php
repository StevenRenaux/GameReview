<?php

namespace App\Twig;

use App\Entity\Genre;
use App\Entity\Platform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigGlobalSubscriber implements EventSubscriberInterface {

    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $manager;

    public function __construct( Environment $twig, EntityManagerInterface $manager ) {
        $this->twig    = $twig;
        $this->manager = $manager;
    }

    public function injectGlobalVariables() {
        $genresGlobal = $this->manager->getRepository(Genre::class)->findAll();
        $this->twig->addGlobal( 'genres', $genresGlobal);

        $platformsGlobal = $this->manager->getRepository(Platform::class)->findAll();
        $this->twig->addGlobal('platforms', $platformsGlobal);
    }

    public static function getSubscribedEvents() {
        return [ KernelEvents::CONTROLLER =>  'injectGlobalVariables' ];
    }
}