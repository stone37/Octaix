<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends AbstractRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return int|mixed|string
     */
    public function getAdmins()
    {
        return $this->createQueryBuilder('t')
                    ->orderBy('t.position', 'asc');
    }

    /**
     * @return int|mixed|string
     */
    public function getEnabled()
    {
        return $this->createQueryBuilder('t')
            ->where('t.enabled = 1')
            ->orderBy('t.position', 'asc')
            ->getQuery()->getResult();
    }

    public function getNumber()
    {
        $qb = $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.enabled = 1');

        return $qb->getQuery()->getSingleScalarResult();
    }

}
