<?php

namespace Dashford\Soundscape\Service;

use Dashford\Soundscape\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;

class ArtistService
{
    private EntityManagerInterface $entityManager;

    private Artist $artist;

    public function __construct(EntityManagerInterface $entityManager, Artist $artist)
    {
        $this->entityManager = $entityManager;
        $this->artist = $artist;
    }

    public function save(array $values)
    {
        $this->artist->setName('blah');
        var_dump($values);
    }
}