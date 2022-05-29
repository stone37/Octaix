<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Trouve une entité par sa clef primaire et renvoie une exception en cas d'absence.
     *
     * @param string|int $id
     * @throws EntityNotFoundException
     */
    public function findOrFail($id): object
    {
        $entity = $this->find($id, null, null);
        if (null === $entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($this->_entityName, [(string) $id]);
        }

        return $entity;
    }

    public function findByCaseInsensitive(array $conditions): array
    {
        return $this->findByCaseInsensitiveQuery($conditions)->getResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCaseInsensitive(array $conditions): ?object
    {
        return $this->findByCaseInsensitiveQuery($conditions)->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Crée une requête qui peut être iterable, mais qui ne récupère les données que lors de la première itération.
     *
     * @param string $indexBy the index for the from
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createIterableQuery(string $alias, $indexBy = null): QueryBuilder
    {
        /** @var IterableQueryBuilder $queryBuilder */
        $queryBuilder = new IterableQueryBuilder($this->_em);

        return $queryBuilder->select($alias)->from($this->_entityName, $alias, $indexBy);
    }

    private function findByCaseInsensitiveQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $k => $v) {
            $conditionString[] = "LOWER(o.$k) = :$k";
            $parameters[$k] = strtolower($v);
        }

        return $this->createQueryBuilder('o')
            ->where(join(' AND ', $conditionString))
            ->setParameters($parameters)
            ->getQuery();
    }
}
