<?php

namespace Dashford\Soundscape\Exception;

use Exception;
use Throwable;

class ValidationException extends Exception implements ExtendedDetailException
{
    private array $invalidRules = [];

    public function __construct($message = "", $code = 0, Throwable $previous = null, array $invalidRules = [])
    {
        parent::__construct($message, $code, $previous);
        $this->invalidRules = $invalidRules;
    }

    public function getDetail(): string
    {
        return implode('\n', $this->invalidRules);
    }
}