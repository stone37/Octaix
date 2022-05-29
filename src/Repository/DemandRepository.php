<?php

namespace App\Repository;

use App\Entity\Demand;
use App\Model\Admin\DemandSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Demand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demand[]    findAll()
 * @method Demand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demand::class);
    }

    public function getDemand(DemandSearch $search)
    {
        $qb = $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'desc');

        if ($search->isEnabled())
            $qb->andWhere('d.enabled = 1');

        return $qb;
    }
}
