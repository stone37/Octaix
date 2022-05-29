<?php

namespace App\Repository;

use App\Entity\Review;
use App\Model\Admin\ReviewSearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


class ReviewRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param ReviewSearch $search
     * @return QueryBuilder|null
     */
    public function getAdmins(ReviewSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.position', 'asc');

        if ($search->isEnabled())
            $qb->andWhere('r.enabled = 1');

        return $qb;
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

    public function findRecent(int $limit)
    {
        return $this->createQueryBuilder('r')
            ->where('r.enabled = true')
            ->andWhere('r.home = true')
            ->orderBy('r.enabled', 'asc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getNumber()
    {
        $qb = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.enabled = true');


        return $qb->getQuery()->getSingleScalarResult();
    }
}
