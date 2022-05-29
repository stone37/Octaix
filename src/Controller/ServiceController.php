<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Settings;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ServiceController extends AbstractController
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
        $services = $em->getRepository(Service::class)->getWithParentNull();
        $reviews = $em->getRepository(Review::class)->findRecent(3);

        $this->breadcrumb($breadcrumbs);

        return $this->render('site/service/index.html.twig', [
            'settings' => $this->settings,
            'services' => $services,
            'reviews' => $reviews,
        ]);
    }

    public function show(Request $request, EntityManagerInterface $em, Breadcrumbs $breadcrumbs)
    {
        $slug = $this->getSlug($request);

        /** @var Service $service */
        $service = $em->getRepository(Service::class)->getBySlug($slug);
        $services = $em->getRepository(Service::class)->getWithParent($service);
        $parent = null;

        if ($service->getParent()) {
            $parent = $service->getParent()->getSlug();

            $this->breadcrumb($breadcrumbs)
                ->addItem($service->getParent()->getName(), $this->generateUrl('app_service_show',
                    ['slug' => $service->getParent()->getSlug()]))
                ->addItem($service->getName());
        } else {
            $this->breadcrumb($breadcrumbs)->addItem($service->getName());
        }

        return $this->render('site/service/show.html.twig', [
            'settings' => $this->settings,
            'service' => $service,
            'services' => $services,
            'serviceSlug' => $slug,
            'parent' => $parent,
        ]);
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @return Breadcrumbs
     */
    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
            ->addItem('Nos solutions', $this->generateUrl('app_service_index'));

        return $breadcrumbs;
    }

    private function getSlug(Request $request)
    {
        if ($request->attributes->get('sub_slug')) {
            $slug = $request->attributes->get('sub_slug');
        } else {
            $slug = $request->attributes->get('slug');
        }

        return $slug;
    }
}
