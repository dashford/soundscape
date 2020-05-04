<?php

use Dashford\Soundscape\Controller\Artist\Create;
use Dashford\Soundscape\Middleware\ErrorHandler;
use Dashford\Soundscape\Middleware\JsonBodyParser;
use Dashford\Soundscape\Service\ArtistService;
use DI\ContainerBuilder;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$env = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$env->load();

// Doctrine Config
require_once __DIR__ . '/../config/doctrine-config.php';

// App Config
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    'settings' => [
        'displayErrorDetails' => getenv('DISPLAY_ERROR_DETAILS'),
        'logErrors' => getenv('LOG_ERRORS'),
        'logErrorDetails' => getenv('LOG_ERROR_DETAILS')
    ]
]);

$containerBuilder->addDefinitions([
    'Psr\Log\LoggerInterface' => function (ContainerInterface $c) {
        $logger = new \Monolog\Logger('soundscape');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', Logger::DEBUG));
        return $logger;
    }
]);
$containerBuilder->addDefinitions([
    'entityManager'=> function () use ($entityManager) {
        return $entityManager;
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Entity\Artist' => function () {
        return new Dashford\Soundscape\Entity\Artist();
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Service\ArtistService' => function (ContainerInterface $c) {
        return new ArtistService(
            $c->get('entityManager'),
            $c->get('Dashford\Soundscape\Entity\Artist')
        );
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Controller\Artist\Create' => function (ContainerInterface $c) {
        return new Create(
            $c->get('Dashford\Soundscape\Service\ArtistService')
        );
    }
]);

$container = $containerBuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(
    $container->get('settings')['displayErrorDetails'],
    $container->get('settings')['logErrors'],
    $container->get('settings')['logErrorDetails'],
    $container->get('Psr\Log\LoggerInterface')
);
$errorMiddleware->setDefaultErrorHandler(
    new ErrorHandler($app->getCallableResolver(),
        $app->getResponseFactory(),
        $container->get('Psr\Log\LoggerInterface')
    )
);

$app->add(new JsonBodyParser());

$app->post('/artist', Create::class);

$app->run();