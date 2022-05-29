<?php

namespace App\Repository;

use App\Entity\Achieve;
use App\Model\Admin\AchieveSearch;
use App\Model\Admin\ProductSearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achieve::class);
    }

    /**
     * @param AchieveSearch $search
     * @return QueryBuilder|null
     */
    public function getAdmins(ProductSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if ($search->isEnabled())
            $qb->andWhere('p.enabled = 1');

        return $qb;
    }

    /**
     * @return int|mixed|string
     */
    public function getEnabled()
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = 1')
            ->getQuery()->getResult();
    }

    /**
     * @param $count
     * @return int|mixed|string
     */
    public function getPartialEnabled($count)
    {
        return $this->createQueryBuilder('p')
                ->where('p.enabled = 1')
                ->setFirstResult(0)
                ->setMaxResults($count)
                ->getQuery()->getResult();
    }


    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNumber()
    {
        return $this->createQueryBuilder('a')
                    ->select('count(a.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}


