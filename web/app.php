<?php

use Dashford\Soundscape\Controller\Api\Artist\Create;
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
            'level' => Logger::DEBUG,
            'uidProcessorLength' => 16
        ]
    ]
]);

$containerBuilder->addDefinitions([
    'Psr\Log\LoggerInterface' => function (ContainerInterface $c) {
        $logger = new \Monolog\Logger('soundscape');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', $c->get('settings')['logger']['level']));
        $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
        $logger->pushProcessor($c->get('Monolog\Processor\UidProcessor'));
        return $logger;
    }
]);
$containerBuilder->addDefinitions([
    'Monolog\Processor\UidProcessor' => function (ContainerInterface $c) {
        return new \Monolog\Processor\UidProcessor($c->get('settings')['logger']['uidProcessorLength']);
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
    'Dashford\Soundscape\Renderer\JsonApiRenderer' => function (ContainerInterface $c) {
        return new \Dashford\Soundscape\Renderer\JsonApiRenderer(
            $c->get('Neomerx\JsonApi\Contracts\Encoder\EncoderInterface'),
            $c->get('Monolog\Processor\UidProcessor')
        );
    }
]);
$containerBuilder->addDefinitions([
    'entityManager'=> function () use ($entityManager) {
        return $entityManager;
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Factory\ArtistFactory' => function (ContainerInterface $c) {
        return new \Dashford\Soundscape\Factory\ArtistFactory($c->get('Psr\Log\LoggerInterface'));
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Service\ArtistService' => function (ContainerInterface $c) {
        return new ArtistService(
            $c->get('Psr\Log\LoggerInterface'),
            $c->get('entityManager'),
            $c->get('Symfony\Component\EventDispatcher\EventDispatcherInterface'),
            $c->get('Dashford\Soundscape\Factory\ArtistFactory')
        );
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Controller\Artist\Create' => function (ContainerInterface $c) {
        return new Create(
            $c->get('Psr\Log\LoggerInterface'),
            $c->get('Dashford\Soundscape\Renderer\JsonApiRenderer'),
            $c->get('Dashford\Soundscape\Service\ArtistService')
        );
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Renderer\JsonApiErrorRenderer' => function (ContainerInterface $c) {
        return new \Dashford\Soundscape\Renderer\JsonApiErrorRenderer(
            $c->get('Neomerx\JsonApi\Contracts\Encoder\EncoderInterface'),
            $c->get('Monolog\Processor\UidProcessor')
        );
    }
]);
$containerBuilder->addDefinitions([
    'Dashford\Soundscape\Subscriber\ArtistSubscriber' => function () {
        return new \Dashford\Soundscape\Subscriber\ArtistSubscriber();
    }
]);
$containerBuilder->addDefinitions([
    'Symfony\Component\EventDispatcher\EventDispatcherInterface' => function (ContainerInterface $c) {
        $dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
        $dispatcher->addSubscriber($c->get('Dashford\Soundscape\Subscriber\ArtistSubscriber'));

        return $dispatcher;
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

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('application/json', $container->get('Dashford\Soundscape\Renderer\JsonApiErrorRenderer'));

$app->add(new JsonBodyParser());

$app->post('/api/v1/artist', Create::class);

$app->run();