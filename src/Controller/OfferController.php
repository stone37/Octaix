<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Settings;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class OfferController extends AbstractController
{
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function index(EntityManagerInterface $em, Breadcrumbs $breadcrumbs)
    {
        if (!$this->settings->isActiveOffre())  throw $this->createNotFoundException('Page introuvable');

        $this->breadcrumb($breadcrumbs);

        $offers = $em->getRepository(Offer::class)->findBy(['enabled' => true], ['createdAt' => 'DESC']);

        return $this->render('site/offer/index.html.twig', [
            'settings' => $this->settings,
            'offers' => $offers,
        ]);
    }

    public function show(EntityManagerInterface $em, Breadcrumbs $breadcrumbs, $slug)
    {
        if (!$this->settings->isActiveOffre())  throw $this->createNotFoundException('Page introuvable');

        /** @var Offer $offer */
        $offer = $em->getRepository(Offer::class)->getBySlug($slug);

        $this->breadcrumb($breadcrumbs)->addItem($offer->getName());

        return $this->render('site/offer/show.html.twig', [
            'settings' => $this->settings,
            'offer' => $offer,
        ]);
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @return Breadcrumbs
     */
    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
            ->addItem('Nos offres', $this->generateUrl('app_offer_index'));

        return $breadcrumbs;
    }
}

