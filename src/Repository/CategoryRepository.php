<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findWithCount(): array
    {
        $data = $this->createQueryBuilder('c')
            ->join('c.posts', 'p')
            ->where('p.online = true')
            ->groupBy('c.id')
            ->select('c', 'COUNT(c.id) as count')
            ->getQuery()
            ->getResult();

        return array_map(function (array $d) {
            $d[0]->setPostsCount((int) $d['count']);

            return $d[0];

        }, $data);
    }

    public function getCategory()
    {
        $results = $this->createQueryBuilder('c')
            ->orderBy('c.name', 'asc')
            ->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result)
            $data[$result['name']] = $result['id'];

        return $data;
    }

    public function getEnabled(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
                ->orderBy('c.name', 'asc');

        return $qb;
    }

    public function getBySlug(string $slug)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()->getOneOrNullResult();
    }
}
