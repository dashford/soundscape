<?php

namespace Dashford\Soundscape\Renderer;

use Monolog\Processor\UidProcessor;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Psr\Http\Message\ResponseInterface as Response;

class JsonApiRenderer
{
    // TODO
    // Introduce types for all these parameters

    private EncoderInterface $encoder;

    private UidProcessor $uidProcessor;

    private Response $response;

    private $data;

    private array $meta = [];

    private $status;

    public function __construct(EncoderInterface $encoder, UidProcessor $uidProcessor)
    {
        $this->encoder = $encoder;
        $this->uidProcessor = $uidProcessor;
    }

    public function buildResponse(Response $response): JsonApiRenderer
    {
        $this->response= $response;

        return $this;
    }

    public function withData($data): JsonApiRenderer
    {
        $this->data = $data;

        return $this;
    }

    public function withMeta(array $meta): JsonApiRenderer
    {
        $this->meta = $meta;

        return $this;
    }

    public function withStatus(int $status): JsonApiRenderer
    {
        $this->status = $status;

        return $this;
    }

    public function respond(): Response
    {
        $this->response->getBody()->write($this->encodeData());

        return $this->response
            ->withStatus($this->status)
            ->withHeader('Content-Type', 'application/json');
    }

    private function encodeData()
    {
        $this->meta['requestId'] = $this->uidProcessor->getUid();
        $this->encoder->withMeta($this->meta);

        return $this->encoder->encodeData($this->data);
    }
}