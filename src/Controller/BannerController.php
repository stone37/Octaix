<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Banner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BannerController extends AbstractController
{
    use ControllerTrait;

    public function index(EntityManagerInterface $em, string $service)
    {
        $banners = $em->getRepository(Banner::class)->getActiveByService($service);

        return $this->render('site/banner/index.html.twig', [
            'banners' => $banners,
            'service' => $service,
        ]);
    }
}
