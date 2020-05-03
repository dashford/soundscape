<?php

use Dashford\Soundscape\Controller\Artist\Save;
use Dashford\Soundscape\Middleware\JsonBodyParser;
use Dashford\Soundscape\Service\ArtistService;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$env->load();

// Doctrine Config
require_once __DIR__ . '/../config/doctrine-config.php';

// App Config
$container = new Container();

$container->set('entityManager', function () use ($entityManager) {
    return $entityManager;
});
$container->set('Dashford\Soundscape\Entity\Artist', function () {
    return new Dashford\Soundscape\Entity\Artist();
});
$container->set('Dashford\Soundscape\Service\ArtistService', function (ContainerInterface $c) {
    return new ArtistService(
        $c->get('entityManager'),
        $c->get('Dashford\Soundscape\Entity\Artist')
    );
});
$container->set('Dashford\Soundscape\Controller\Artist\Save', function (ContainerInterface $c) {
    return new Save(
        $c->get('Dashford\Soundscape\Service\ArtistService')
    );
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(new JsonBodyParser());

$app->post('/artist', Save::class);

$app->run();