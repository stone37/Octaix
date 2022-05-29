<?php

namespace App\Repository;

use App\Entity\Taxe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taxe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxe[]    findAll()
 * @method Taxe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxe::class);
    }


}
