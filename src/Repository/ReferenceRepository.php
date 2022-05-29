<?php

namespace App\Repository;

use App\Entity\Reference;
use Doctrine\Persistence\ManagerRegistry;

class ReferenceRepository extends AbstractRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reference::class);
    }

    /**
     * @return int|mixed|string
     */
    public function getAdmins()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.position', 'asc');
    }

    /**
     * @return int|mixed|string
     */
    public function getEnabled()
    {
        return $this->createQueryBuilder('r')
            ->where('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->getQuery()->getResult();
    }

    public function findLimit(int $limit)
    {
        return $this->createQueryBuilder('r')
            ->where('r.enabled = 1')
            ->orderBy('r.position', 'asc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
