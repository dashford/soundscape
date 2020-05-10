<?php

namespace Dashford\Soundscape\Controller\Api\Artist;

use Dashford\Soundscape\Exception\ValidationException;
use Dashford\Soundscape\Renderer\JsonApiRenderer;
use Dashford\Soundscape\Service\ArtistService;
use Dashford\Soundscape\Value\HTTPStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class Create
{
    private LoggerInterface $logger;

    private JsonApiRenderer $jsonApiResponse;

    private ArtistService $artistService;

    public function __construct(LoggerInterface $logger, JsonApiRenderer $jsonApiResponse, ArtistService $artistService)
    {
        $this->logger = $logger;
        $this->jsonApiResponse = $jsonApiResponse;
        $this->artistService = $artistService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $artist = $this->artistService->create($request->getParsedBody());
        } catch (ValidationException $e) {
            $this->logger->error('error');
            throw new HttpBadRequestException($request, 'Validation failed', $e);
        }

        return $this->jsonApiResponse->buildResponse($response)
            ->withData($artist)
            ->withStatus(HTTPStatus::CREATED)
            ->respond();
    }
}