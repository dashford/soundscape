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

    private Artist $artistEntity;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, Artist $artistEntity)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->artistEntity = $artistEntity;
    }

    public function create(array $values)
    {
        try {
            v::key('name', v::stringType())->assert($values);
        } catch (Exception $e) {
            throw new ValidationException('Validation failed', HTTPStatus::BAD_REQUEST, null, $e->getMessages());
        }

//        $this->artistEntity->setName($values['name']);
//        $this->entityManager->persist($this->artistEntity);
//        $this->entityManager->flush();
    }
}