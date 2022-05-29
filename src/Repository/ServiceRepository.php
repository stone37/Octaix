<?php

namespace App\Repository;

use App\Entity\Service;
use App\Model\Admin\ServiceSearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ServiceRepository extends AbstractRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function getAdmins(ServiceSearch $search, $parent = null): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');

        if ($parent)
            $qb->where('s.parent = :parent')->setParameter('parent', $parent);
        else
            $qb->where($qb->expr()->isNull('s.parent'));

        $qb->orderBy('s.position', 'asc');;

        if ($search->isEnabled())
            $qb->andWhere('s.enabled = 1');

        if ($search->getName())
            $qb->andWhere('s.name LIKE :name')->setParameter('name', '%'.$search->getName().'%');

        return $qb;
    }

    public function getEnabledData(): ?array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'asc')
            ->andWhere('s.enabled = 1');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result)
            $data[$result['name']] = $result['id'];

        return $data;
    }

    public function getEnabled(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.position', 'asc')
            ->andWhere('s.enabled = 1');

        return $qb;
    }

    public function getEnabledWithParentNull(): ?QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');

        $qb->orderBy('s.position', 'asc')
            ->where($qb->expr()->isNull('s.parent'))
            ->andWhere('s.enabled = 1');

        return $qb;
    }

    public function getWithParentNull()
    {
        $qb = $this->createQueryBuilder('s')
                ->leftJoin('s.children', 'children')
                ->addSelect('children');

        $qb->where($qb->expr()->isNull('s.parent'))
            ->andWhere('s.enabled = 1')
            ->orderBy('s.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function getWithParent(Service $service)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.children', 'children')
            ->addSelect('children');

        $qb->where($qb->expr()->isNull('s.parent'))
            ->andWhere('s.enabled = 1')
            ->andWhere('s.id <> :id')
            ->orderBy('s.position', 'asc')
            ->setParameter('id', $service->getId());

        return $qb->getQuery()->getResult();
    }

    public function getIsHome()
    {
        return $this->createQueryBuilder('s')
                ->leftJoin('s.children', 'children')
                ->addSelect('children')
                ->where('s.enabled = 1')
                ->andWhere('s.isHome = 1')
                ->orderBy('s.position', 'asc')
                ->getQuery()->getResult();
    }

    public function getBySlug(string $slug)
    {
        return $this->createQueryBuilder('s')
                ->where('s.enabled = 1')
                ->andWhere('s.slug = :slug')
                ->leftJoin('s.children', 'children')
                ->addSelect('children')
                ->setParameter('slug', $slug)
                ->getQuery()->getOneOrNullResult();
    }

    public function getNumber()
    {
        return $this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->where('s.enabled = 1')
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function getServiceParentNull()
    {
        $qb = $this->createQueryBuilder('s');

        $qb->where($qb->expr()->isNull('s.parent'))
            ->orderBy('s.position', 'asc');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result)
            $data[$result['name']] = $result['slug'];

        return $data;
    }
}
