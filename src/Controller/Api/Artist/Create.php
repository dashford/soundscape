<?php

namespace Dashford\Soundscape\Controller\Api\Artist;

use Dashford\Soundscape\Service\ArtistService;
use Dashford\Soundscape\Value\HTTPStatus;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class Create
{
    private LoggerInterface $logger;

    private EncoderInterface $encoder;

    private ArtistService $artistService;

    public function __construct(LoggerInterface $logger, EncoderInterface $encoder, ArtistService $artistService)
    {
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->artistService = $artistService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $artist = $this->artistService->create($request->getParsedBody());

        var_dump($this->encoder->encodeData($artist));

        return $response->withStatus(HTTPStatus::CREATED)
            ->withBody($this->encoder->encodeData($artist))
            ->withHeader('Content-Type', 'application/json');
    }
}