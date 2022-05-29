<?php

namespace App\Repository;


use App\Entity\EmailVerification;
use App\Entity\User;
use App\Orm\CleanableRepositoryInterface;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<EmailVerification>
 */
class EmailVerificationRepository extends AbstractRepository implements CleanableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailVerification::class);
    }

    /**
     * @param User $user
     * @return EmailVerification|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastForUser(User $user): ?EmailVerification
    {
        return $this->createQueryBuilder('v')
            ->where('v.author = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime les anciennes demande de verification d'email.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('v')
            ->where('v.createdAt < :date')
            ->setParameter('date', new DateTime('-1 month'))
            ->delete(EmailVerification::class, 'v')
            ->getQuery()
            ->execute();
    }
}
