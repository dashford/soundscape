<?php

namespace Dashford\Soundscape\Factory;

use Dashford\Soundscape\Entity\Artist;
use Psr\Log\LoggerInterface;

class ArtistFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createArtist(): Artist
    {
        return new Artist($this->logger);
    }
}