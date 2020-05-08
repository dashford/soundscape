<?php

namespace Dashford\Soundscape\Service;

use Dashford\Soundscape\Entity\Artist;
use Dashford\Soundscape\Exception\ValidationException;
use Dashford\Soundscape\Value\HTTPStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\Exception;
use Respect\Validation\Validator as v;

class ArtistService
{
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;

    private Artist $artist;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, Artist $artist)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->artist = $artist;
    }

    public function create(array $values)
    {
        try {
            v::key('name', v::stringType())->assert($values);
        } catch (Exception $e) {
            throw new ValidationException('Validation failed', HTTPStatus::BAD_REQUEST, null, $e->getMessages());
        }

        $this->artist->setName($values['name']);
        $this->entityManager->persist($this->artist);
        $this->entityManager->flush();

        return $this->artist;
    }
}