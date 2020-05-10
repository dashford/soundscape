<?php

namespace Dashford\Soundscape\Renderer;

use Monolog\Processor\UidProcessor;
use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Schema\Error;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class JsonApiErrorRenderer implements ErrorRendererInterface
{
    private EncoderInterface $encoder;

    private UidProcessor $uidProcessor;

    public function __construct(EncoderInterface $encoder, UidProcessor $uidProcessor)
    {
        $this->encoder = $encoder;
        $this->uidProcessor = $uidProcessor;
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $error = new Error(
            $this->uidProcessor->getUid(),
            null,
            null,
            $exception->getCode(),
            null,
            $exception->getMessage(),
            'detail'
        );

        return $this->encoder->encodeError($error);
    }
}