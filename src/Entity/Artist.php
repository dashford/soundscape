<?php

namespace Dashford\Soundscape\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="artists")
 * @ORM\HasLifecycleCallbacks()
 */
class Artist
{
    private LoggerInterface $logger;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updated;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreationTimes(): void
    {
        $now = new DateTime(null, new DateTimeZone('UTC'));
        $this->created = $now;
        $this->updated = $now;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdated(): void
    {
        $this->updated = new DateTime(null, new DateTimeZone('UTC'));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}