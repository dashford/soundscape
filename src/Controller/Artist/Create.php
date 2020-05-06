<?php

namespace Dashford\Soundscape\Controller\Artist;

use Dashford\Soundscape\Service\ArtistService;
use Dashford\Soundscape\Value\HTTPStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class Create
{
    private LoggerInterface $logger;

    private ArtistService $artistService;

    public function __construct(LoggerInterface $logger, ArtistService $artistService)
    {
        $this->logger = $logger;
        $this->artistService = $artistService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->artistService->create($request->getParsedBody());

        return $response->withStatus(HTTPStatus::CREATED)
            ->withHeader('Content-Type', 'application/json');
    }
}