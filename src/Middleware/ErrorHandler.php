<?php

namespace Dashford\Soundscape\Middleware;

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

    private ServerRequestInterface $request;

    private Throwable $exception;

    public function __construct(CallableResolverInterface $callableResolver, ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        $this->callableResolver = $callableResolver;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $this->logger->info('boom');
        $this->request = $request;
        $this->exception = $exception;

        return $this->responseFactory->createResponse(404);
    }
}