<?php

namespace App\Repository;

use App\Entity\Command;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $CommandBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $CommandBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    public function findOneById($id): ?Command
    {
        try {
            $qb = $this->createQueryBuilder('c')
                ->where('c.id = :id')
                ->setParameter('id', $id)
                ->leftJoin('c.items', 'items')
                ->leftJoin('c.user', 'user')
                ->leftJoin('c.advert', 'advert')
                ->addSelect('items')
                ->addSelect('user')
                ->addSelect('advert')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }

        return $qb;
    }

    /**
     * @param User|UserInterface $user
     * @return mixed
     */
    public function byFacture(User $user)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.validated = 1')
            ->andWhere('c.reference != 0')
            ->leftJoin('c.user', 'user')
            ->leftJoin('c.advert', 'advert')
            ->leftJoin('c.produits', 'produits')
            ->addSelect('user')
            ->addSelect('advert')
            ->addSelect('produits')
            ->orderBy('c.createdAt', 'desc')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Command[] Returns an array of Command objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->CommandBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Command
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
