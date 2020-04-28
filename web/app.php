<?php

use Dashford\Soundscape\Controller\ArtistController;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$env->load();


// Doctrine Config
$paths = array(__DIR__ . '/../src');
$isDevMode = false;

// the connection configuration
$dbParams = [
    'driver'   => 'pdo_mysql',
    'user'     => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'dbname'   => getenv('DB_NAME'),
];

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);


// App Config
$container = new Container();

$container->set('entityManager', function () use ($entityManager) {
    return $entityManager;
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/artist/{name}', ArtistController::class . ':output');

$app->run();