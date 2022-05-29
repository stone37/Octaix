<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Content>
 */
class ContentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    /**
     * @return IterableQueryBuilder|QueryBuilder<Content>
     */
    public function findLatest(int $limit = 5): ?IterableQueryBuilder
    {
        return $this->createIterableQuery('c')
            ->orderBy('c.createdAt', 'DESC')
            ->where('c.online = TRUE')
            ->setMaxResults($limit);
    }
}
