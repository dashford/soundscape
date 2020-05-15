<?php

namespace Dashford\Soundscape\Renderer;

use Dashford\Soundscape\Exception\ExtendedDetailException;
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
        if ($exception->getPrevious() instanceof ExtendedDetailException) {
            $detail = $exception->getPrevious()->getDetail();
        }
        $error = new Error(
            $this->uidProcessor->getUid(),
            null,
            null,
            $exception->getCode(),
            null,
            $exception->getMessage(),
            $detail ?? null
        );

        return $this->encoder->encodeError($error);
    }
}