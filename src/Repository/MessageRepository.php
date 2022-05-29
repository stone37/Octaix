<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Advert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends AbstractRepository<Message>
 */
class MessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param User|UserInterface $user
     * @return int|mixed|string
     */
    public function getByUserSend(User $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.advert', 'advert')
            ->addSelect('advert')
            ->where('m.email = :email')
            ->andWhere('m.deleted = 0')
            ->setParameter('email', $user->getEmail())
            ->orderBy('m.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User|UserInterface $user
     * @return int|mixed|string
     */
    public function getByUserReceive(User $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.advert', 'advert')
            ->addSelect('advert')
            ->where('advert.user = :user')
            ->andWhere('m.recepDeleted = 0')
            ->orderBy('m.createdAt', 'desc')
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User|UserInterface $user
     * @return \Doctrine\ORM\QueryBuilder|int|mixed|string
     */
    public function getByUserSendNumber(User $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.email = :email')
            ->andWhere('m.deleted = 0')
            ->setParameter('email', $user->getEmail());

        try {
            $qb = $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {} catch (NoResultException $e) {}

        return $qb;
    }

    /**
     * @param User|UserInterface $user
     * @return \Doctrine\ORM\QueryBuilder|int|mixed|string
     */
    public function getByUserReceiveNumber(User $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->leftJoin('m.advert', 'advert')
            ->where('advert.user = :user')
            ->andWhere('m.recepDeleted = 0')
            ->setParameter('user', $user);

        try {
            $qb = $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {} catch (NoResultException $e) {}

        return $qb;
    }

    public function deleteForUser(User $user): void
    {
        $this->createQueryBuilder('m')
            ->where('m.email = :email')
            ->setParameter('email', $user->getEmail())
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function deletedSendForUser(User $user): void
    {
        $this->createQueryBuilder('m')
            ->where('m.email = :email')
            ->andWhere('m.deleted = 1')
            ->setParameter('email', $user->getEmail())
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function deletedReceiveForUser(User $user): void
    {
        $this->createQueryBuilder('m')
            ->leftJoin('m.advert', 'advert')
            ->where('advert.user = :user')
            ->andWhere('m.recepDeleted = 1')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function deleteMessages(): void
    {
        $this->createQueryBuilder('m')
            ->where('m.recepDeleted = 1')
            ->andWhere('m.deleted = 1')
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * Force l'hydratation des messages (pour éviter de faire n+1 requêtes).
     */
    public function hydrateMessages(Advert $advert): Advert
    {
        $messages = $this->createQueryBuilder('m')
            ->where('m.advert = :advert')
            ->join('m.author', 'u')
            ->select('m, partial u.{id, username, email}')
            ->setParameter('advert', $advert)
            ->orderBy('m.accepted', 'DESC')
            ->addOrderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
        $advert->setMessages(new ArrayCollection($messages));

        return $advert;
    }
}
