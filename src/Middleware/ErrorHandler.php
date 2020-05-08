<?php

namespace Dashford\Soundscape\Middleware;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Schema\Error;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;

class ErrorHandler
{
    private CallableResolverInterface $callableResolver;

    private ResponseFactoryInterface $responseFactory;

    private LoggerInterface $logger;

    private EncoderInterface $encoder;

    private ServerRequestInterface $request;

    private Throwable $exception;

    public function __construct(CallableResolverInterface $callableResolver, ResponseFactoryInterface $responseFactory, LoggerInterface $logger, EncoderInterface $encoder)
    {
        $this->callableResolver = $callableResolver;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $error = new Error(
            'some-id'
        );

        var_dump($this->encoder->encodeError($error));
        $this->logger->info('boom');
        $this->request = $request;
        $this->exception = $exception;

        var_dump($this->exception->getMessage());

        return $this->responseFactory->createResponse(400);
    }
}