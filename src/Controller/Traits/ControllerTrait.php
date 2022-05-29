<?php

namespace App\Controller\Traits;

use App\Entity\Category;
use App\Entity\User;
use App\Manager\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * Trait ControllerTrait
 * @package App\Controller\Traits
 */
Trait ControllerTrait
{
    public function getUsers(EntityManagerInterface $manager, $id): ?User
    {
        return $manager->getRepository(User::class)->getUser($id);
    }

    public function getCategories(EntityManagerInterface $manager)
    {
        return $manager->getRepository(Category::class)->getWithParentNull();
    }

    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Acceuil', $this->generateUrl('app_home'));

        return $breadcrumbs;
    }
}

