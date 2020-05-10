<?php

namespace Dashford\Soundscape\Repository;

use Doctrine\ORM\EntityRepository;

class ArtistRepository extends EntityRepository
{
    public function getRecentArtists($number = 10)
    {
        $dql = 'SELECT name FROM artist ORDER BY created DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($number);
        return $query->getResult();
    }
}