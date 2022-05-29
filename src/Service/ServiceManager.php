<?php

namespace App\Service;


use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class ServiceManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get()
    {
        return $this->em->getRepository(Service::class)->getWithParentNull();
    }
}