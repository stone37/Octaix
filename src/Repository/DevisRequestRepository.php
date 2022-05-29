<?php

namespace App\Repository;

use App\Entity\DevisRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DevisRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisRequest::class);
    }

    public function findLastRequestForIp(string $ip): ?DevisRequest
    {
        return $this->createQueryBuilder('req')
            ->where('req.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
