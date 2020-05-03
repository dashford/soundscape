<?php

namespace Dashford\Soundscape\Service;

use Dashford\Soundscape\Entity\Artist as ArtistEntity;
use Doctrine\ORM\EntityManagerInterface;

class ArtistService
{
    private EntityManagerInterface $entityManager;

    private ArtistEntity $artistEntity;

    public function __construct(EntityManagerInterface $entityManager, ArtistEntity $artistEntity)
    {
        $this->entityManager = $entityManager;
        $this->artistEntity = $artistEntity;
    }

    public function create(array $values)
    {
        // validation here
        $this->artistEntity->setName('blah');
        var_dump($values);
    }
}