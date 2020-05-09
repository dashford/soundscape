<?php

namespace Dashford\Soundscape\Middleware;

use Monolog\Processor\UidProcessor;
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

    private UidProcessor $uidProcessor;

    private ServerRequestInterface $request;

    private Throwable $exception;

    public function __construct(CallableResolverInterface $callableResolver, ResponseFactoryInterface $responseFactory, LoggerInterface $logger, EncoderInterface $encoder, UidProcessor $uidProcessor)
    {
        $this->callableResolver = $callableResolver;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->uidProcessor = $uidProcessor;
    }

    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $this->request = $request;
        $this->exception = $exception;

        $error = new Error(
            $this->uidProcessor->getUid(),
            null,
            null,
            $this->exception->getCode(),
            'code',
            $this->exception->getMessage(),
            'detail'
        );

        $response =  $this->responseFactory->createResponse();
        $response->getBody()->write($this->encoder->encodeError($error));

        return $response->withStatus($this->exception->getCode())
            ->withHeader('Content-Type', 'application/json');
    }
}