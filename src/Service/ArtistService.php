<?php

namespace Dashford\Soundscape\Service;

use Dashford\Soundscape\Entity\Artist;
use Dashford\Soundscape\Event\ArtistCreatedEvent;
use Dashford\Soundscape\Exception\ValidationException;
use Dashford\Soundscape\Value\HTTPStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\Exception;
use Respect\Validation\Validator as v;

class ArtistService
{
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;

    private EventDispatcherInterface $eventDispatcher;

    private Artist $artist;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, Artist $artist)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->artist = $artist;
    }

    public function create(array $values): Artist
    {
        try {
            v::key('name', v::stringType())->assert($values);
        } catch (Exception $e) {
            throw new ValidationException('Validation failed', HTTPStatus::BAD_REQUEST, null, $e->getMessages());
        }

        $this->artist->setName($values['name']);
        $this->entityManager->persist($this->artist);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new ArtistCreatedEvent($this->artist), ArtistCreatedEvent::NAME);

        return $this->artist;
    }
}