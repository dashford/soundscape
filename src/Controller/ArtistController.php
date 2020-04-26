<?php

namespace Dashford\Soundscape\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ArtistController
{
    public function __construct()
    {

    }

    public function output(Request $request, Response $response, array $args)
    {
        $response->getBody()->write("Controller for " . $args['name']);
        return $response;
    }
}