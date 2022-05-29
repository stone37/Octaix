<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Notification;
use App\Orm\CleanableRepositoryInterface;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Notification>
 */
class NotificationRepository extends AbstractRepository implements CleanableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param string[] $channels
     *
     * @return Notification[]
     */
    public function findRecentForUser(User $user, array $channels = ['public']): array
    {
        return array_map(function ($n, $user) {(clone $n)->setUser($user);}, $this->createQueryBuilder('notif')
            ->orderBy('notif.createdAt', 'DESC')
            ->setMaxResults(10)
            ->where('notif.user = :user')
            ->orWhere('notif.user IS NULL AND notif.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult());
    }

    /**
     * Persiste une nouvelle notification ou met à jour une notification précédente.
     *
     * @param Notification $notification
     * @return object
     * @throws \Doctrine\ORM\ORMException
     */
    public function persistOrUpdate(Notification $notification): object
    {
        if (null === $notification->getUser()) {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }

        $oldNotification = $this->findOneBy([
            'user' => $notification->getUser(),
            'target' => $notification->getTarget(),
        ]);

        if ($oldNotification) {
            $oldNotification->setCreatedAt($notification->getCreatedAt());
            $oldNotification->setMessage($notification->getMessage());

            return $oldNotification;
        } else {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }
    }

    /**
     * Supprime les anciennes notifications.
     */
    public function clean(): int
    {
        return $this->createQueryBuilder('n')
            ->where('n.createdAt < :date')
            ->setParameter('date', new DateTime('-3 month'))
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute();
    }
}
