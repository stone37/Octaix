<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return User
     */
    public function createUser()
    {
        return new User();
    }

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository(User::class);
    }

    /**
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}