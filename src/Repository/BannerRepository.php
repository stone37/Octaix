<?php

namespace App\Repository;

use App\Entity\Banner;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    public function getAdmin()
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'desc');

        return $qb;
    }

    public function getActiveByService(string $service)
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.enabled = 1')
            ->andWhere('b.services LIKE :service')
            ->andWhere('b.startDate <= :start')
            ->andWhere('b.endDate >= :end')
            ->setParameter('service', '%'.$service.'%')
            ->setParameter('start', new DateTime())
            ->setParameter('end', new DateTime())
            ->orderBy('b.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

}
