<?php

namespace Dashford\Soundscape\Exception;

interface ExtendedDetailException
{
    public function getDetail(): string;
}