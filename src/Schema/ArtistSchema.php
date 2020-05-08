<?php

namespace Dashford\Soundscape\Schema;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\BaseSchema;

class ArtistSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'artist';
    }

    public function getId($resource): ?string
    {
        return '12345sdksjndd';
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'test' => 'test1'
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}