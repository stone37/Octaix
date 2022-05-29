<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Achieve;
use App\Entity\Post;
use App\Entity\Reference;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;


class HomeController extends AbstractController
{
    use ControllerTrait;

    public function index(EntityManagerInterface $em)
    {
        $services = $em->getRepository(Service::class)->getIsHome();
        $references = $em->getRepository(Reference::class)->findLimit(6);
        $posts = $em->getRepository(Post::class)->findRecent(3);

        return $this->render('site/home/index.html.twig', [
            'services' => $services,
            'references' => $references,
            'posts' => $posts,
        ]);
    }

    public function apropos(EntityManagerInterface $em, Breadcrumbs $breadcrumbs)
    {
        $this->breadcrumb($breadcrumbs)->addItem('A propos');

        $teams = $em->getRepository(Team::class)->findBy(['enabled' => true], ['position' => 'asc']);
        $reviews = $em->getRepository(Review::class)->findRecent(4);

        return $this->render('site/home/apropos.html.twig', [
            'teams' => $teams,
            'reviews' => $reviews,
        ]);
    }
}
