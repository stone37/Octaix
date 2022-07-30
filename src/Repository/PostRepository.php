<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use App\Model\Admin\PostSearch;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Post>
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findRecent(int $limit)
    {
        return $this->createIterableQuery('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findRecentBySlug(int $limit, $slug)
    {
        return $this->createIterableQuery('p')
            ->select('p')
            ->where('p.online = true')
            ->andWhere('p.slug <> :slug')
            ->setParameter('slug', $slug)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function queryAll(?Category $category = null): Query
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC');

        if ($category) {
            $query = $query
                ->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        return $query->getQuery();
    }

    public function getPosts()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.online = true')
            ->orderBy('p.createdAt', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function getAdminPosts(PostSearch $search)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'category')
            ->addSelect('category')
            ->orderBy('p.createdAt', 'desc');

        if ($search->getCategory())
            $qb->andWhere($qb->expr()->in('p.category', [$search->getCategory()]));

        if ($search->isPublished())
            $qb->andWhere('p.online = 1');

        return $qb;
    }

    public function getByCategory(Category $category)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.online = true')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.createdAt', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function getNumber()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.online = true');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
