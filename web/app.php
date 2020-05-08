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
$env->required('DISPLAY_ERROR_DETAILS')->isBoolean();

// Doctrine Config
require_once __DIR__ . '/../config/doctrine-config.php';

// App Config
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    'settings' => [
        'displayErrorDetails' => filter_var(getenv('DISPLAY_ERROR_DETAILS'), FILTER_VALIDATE_BOOLEAN),
        'logErrors' => filter_var(getenv('LOG_ERRORS'), FILTER_VALIDATE_BOOLEAN),
        'logErrorDetails' => filter_var(getenv('LOG_ERROR_DETAILS'), FILTER_VALIDATE_BOOLEAN),
        'logger' => [
            'level' => Logger::DEBUG
        ]
    ]
]);

$containerBuilder->addDefinitions([
    'Psr\Log\LoggerInterface' => function (ContainerInterface $c) {
        $logger = new \Monolog\Logger('soundscape');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', $c->get('settings')['logger']['level']));
        $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        return $logger;
    }
]);
$containerBuilder->addDefinitions([
    'Neomerx\JsonApi\Contracts\Encoder\EncoderInterface' => function (ContainerInterface $c) {
        return \Neomerx\JsonApi\Encoder\Encoder::instance([
            'Dashford\Soundscape\Entity\Artist' => 'Dashford\Soundscape\Schema\ArtistSchema'
        ])
        ->withUrlPrefix('https://soundscape.internal/api/v1')
        ->withEncodeOptions(JSON_PRETTY_PRINT);
    }
]);
$containerBuilder->addDefinitions([
    'entityManager'=> function () use ($entityManager) {
        return $entityManager;
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Entity\Artist' => function (ContainerInterface $c) {
        return new Dashford\Soundscape\Entity\Artist($c->get('Psr\Log\LoggerInterface'));
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Service\ArtistService' => function (ContainerInterface $c) {
        return new ArtistService(
            $c->get('Psr\Log\LoggerInterface'),
            $c->get('entityManager'),
            $c->get('Dashford\Soundscape\Entity\Artist')
        );
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Controller\Artist\Create' => function (ContainerInterface $c) {
        return new Create(
            $c->get('Psr\Log\LoggerInterface'),
            $c->get('Neomerx\JsonApi\Contracts\Encoder\EncoderInterface'),
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

$app->post('/api/v1/artist', Create::class);

$app->run();