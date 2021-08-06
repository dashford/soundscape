<?php

namespace Dashford\Soundscape\Schema\Soundscape;

class Artist
{
    private array $values = [];

    private ?string $name = null;

    public function initialise(array $values): void
    {
        $this->values = $values;
        $this->setName();
    }

    public function assertNameIsValid(): bool
    {
        return false;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    private function setName()
    {
        $this->name = $this->values['name'] ?? null;
    }
}