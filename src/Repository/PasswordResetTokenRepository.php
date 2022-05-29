<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Orm\CleanableRepositoryInterface;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends AbstractRepository implements CleanableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    /**
     * Supprime les anciennes demande de verification d'email.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('p')
            ->where('p.createdAt < :date')
            ->setParameter('date', new DateTime('-1 day'))
            ->delete(PasswordResetToken::class, 'p')
            ->getQuery()
            ->execute();
    }
}
