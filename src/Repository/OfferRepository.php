<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Model\Admin\OfferSearch;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Offer>
 */
class OfferRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }


    public function getAdmins(OfferSearch $search)
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'desc');

        if ($search->isEnabled())
            $qb->andWhere('o.enabled = 1');

        return $qb;
    }

    public function getBySlug(string $slug)
    {
        return $this->createQueryBuilder('o')
            ->where('o.slug = :slug')
            ->andWhere('o.enabled = 1')
            ->setParameter('slug', $slug)
            ->getQuery()->getOneOrNullResult();
    }

    public function getNumber()
    {
        $qb = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where('o.enabled = 1');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
