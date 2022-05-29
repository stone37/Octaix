<?php

namespace App\Controller\Admin;

use App\Entity\Achieve;
use App\Entity\Offer;
use App\Entity\Post;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DashboardController extends AbstractController
{
    public function index(EntityManagerInterface $em)
    {
        $admins  = $em->getRepository(User::class)->getAdminNumber();
        $achieve = $em->getRepository(Achieve::class)->getNumber();
        $review  = $em->getRepository(Review::class)->getNumber();
        $team    = $em->getRepository(Team::class)->getNumber();
        $service = $em->getRepository(Service::class)->getNumber();
        $post    = $em->getRepository(Post::class)->getNumber();
        $offer   = $em->getRepository(Offer::class)->getNumber();

        return $this->render('admin/dashboard/index.html.twig', [
            'achieve' => $achieve,
            'review'  => $review,
            'team'    => $team,
            'service' => $service,
            'admins'  => $admins,
            'post'    => $post,
            'offer'   => $offer,
        ]);
    }
}

