<?php

namespace Dashford\Soundscape\Event;

use Dashford\Soundscape\Entity\Artist;
use Symfony\Contracts\EventDispatcher\Event;

class ArtistCreatedEvent extends Event
{
    public const NAME = 'artist.created';

    protected Artist $artist;

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    public function getArtist()
    {
        return $this->artist;
    }
}