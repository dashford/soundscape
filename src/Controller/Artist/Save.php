<?php

namespace Dashford\Soundscape\Controller\Artist;

use Dashford\Soundscape\Service\ArtistService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Save
{
    private ArtistService $artistService;

    public function __construct(ArtistService $artistService)
    {
        $this->artistService = $artistService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->artistService->save($request->getParsedBody());

        return $response;
    }
}