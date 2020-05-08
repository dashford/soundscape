<?php

namespace Dashford\Soundscape\Controller\Api\Artist;

use Dashford\Soundscape\Response\JsonApiResponse;
use Dashford\Soundscape\Service\ArtistService;
use Dashford\Soundscape\Value\HTTPStatus;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class Create
{
    private LoggerInterface $logger;

    private JsonApiResponse $jsonApiResponse;

    private ArtistService $artistService;

    public function __construct(LoggerInterface $logger, JsonApiResponse $jsonApiResponse, ArtistService $artistService)
    {
        $this->logger = $logger;
        $this->jsonApiResponse = $jsonApiResponse;
        $this->artistService = $artistService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $artist = $this->artistService->create($request->getParsedBody());

        return $this->jsonApiResponse->buildResponse($response)
            ->withData($artist)
            ->withStatus(HTTPStatus::CREATED)
            ->respond();
    }
}