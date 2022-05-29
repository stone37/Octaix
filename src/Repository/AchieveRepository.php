<?php

namespace App\Repository;

use App\Entity\Achieve;
use App\Model\Admin\AchieveSearch;
use App\Model\Search;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class AchieveRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achieve::class);
    }

    /**
     * @param AchieveSearch $search
     * @return QueryBuilder|null
     */
    public function getAdmins(AchieveSearch $search): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        $qb->leftJoin('a.service', 'service')
            ->addSelect('service')
            ->orderBy('a.position', 'asc');

        if ($search->getService())
            $qb->andWhere('a.service = :service')->setParameter('service', $search->getService());

        if ($search->isEnabled())
            $qb->andWhere('a.enabled = 1');

        return $qb;
    }

    /**
     * @return int|mixed|string
     */
    public function getEnabled()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.service', 'service')
            ->addSelect('service')
            ->where('a.enabled = 1')
            ->orderBy('a.position', 'asc')
            ->getQuery()->getResult();
    }


    public function searchEnabled(Search $search)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.service', 'service')
            ->addSelect('service')
            ->where('a.enabled = 1')
            ->orderBy('a.position', 'asc');

        if ($search->getData())
            $qb->andWhere('service.slug = :slug')->setParameter('slug', $search->getData());

        return $qb;
    }

    /**
     * @param $count
     * @return int|mixed|string
     */
    public function getPartialEnabled($count)
    {
        return $this->createQueryBuilder('a')
                ->leftJoin('a.service', 'service')
                ->addSelect('service')
                ->where('a.enabled = 1')
                ->orderBy('a.position', 'asc')
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
                    ->where('a.enabled = 1')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getBySlug(string $slug)
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = 1')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()->getOneOrNullResult();
    }
}
