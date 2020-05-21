<?php

namespace Dashford\Soundscape\Service;

use Dashford\Soundscape\Entity\Artist;
use Dashford\Soundscape\Event\ArtistCreatedEvent;
use Dashford\Soundscape\Exception\ValidationException;
use Dashford\Soundscape\Factory\ArtistFactory;
use Dashford\Soundscape\Value\HTTPStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\Exception;
use Respect\Validation\Validator as v;

class ArtistService
{
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;

    private EventDispatcherInterface $eventDispatcher;

    private ArtistFactory $artistFactory;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, ArtistFactory $artistFactory)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->artistFactory = $artistFactory;
    }

    public function create(array $values): Artist
    {
        // images {}
        // tags {}
        try {
            v::key('name', v::stringType())->assert($values);
            v::key('musicbrainz_id', v::uuid(4), false)->assert($values);
            if (isset($values['bio'])) {
                v::keySet(
                    v::key('summary', v::stringType()),
                    v::key('content', v::stringType())
                )->assert($values['bio']);
            }
        } catch (Exception $e) {
            throw new ValidationException('Validation failed', HTTPStatus::BAD_REQUEST, null, $e->getMessages());
        }

        $artist = $this->artistFactory->createArtist();

        $artist->setName($values['name']);
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new ArtistCreatedEvent($artist), ArtistCreatedEvent::NAME);

        return $artist;
    }
}