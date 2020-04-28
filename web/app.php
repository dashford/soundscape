<?php

use Dashford\Soundscape\Controller\ArtistController;
use DI\Container;
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

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/artist/{name}', ArtistController::class . ':output');

$app->run();