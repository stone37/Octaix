<?php

namespace App\Controller;

use App\Entity\Achieve;
use App\Entity\Settings;
use App\Form\Filter\SearchType;
use App\Model\Search;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AchieveController extends AbstractController
{
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function index(
        Request $request,
        EntityManagerInterface $em,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator)
    {
        if (!$this->settings->isActiveAchieve())  throw $this->createNotFoundException('Page introuvable');

        $this->breadcrumb($breadcrumbs);

        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $data = $em->getRepository(Achieve::class)->searchEnabled($search);

        $achieves = $paginator->paginate($data, $request->query->getInt('page', 1), 30);

        return $this->render('site/achieve/index.html.twig', [
            'settings' => $this->settings,
            'achieves' => $achieves,
            'form' => $form->createView(),
        ]);
    }

    public function show(EntityManagerInterface $em, Breadcrumbs $breadcrumbs, $slug)
    {
        if (!$this->settings->isActiveAchieve())  throw $this->createNotFoundException('Page introuvable');

        /** @var Achieve $achieve */
        $achieve = $em->getRepository(Achieve::class)->getBySlug($slug);

        $this->breadcrumb($breadcrumbs)->addItem($achieve->getTitle());

        return $this->render('site/achieve/show.html.twig', [
            'settings' => $this->settings,
            'achieve' => $achieve,
        ]);
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @return Breadcrumbs
     */
    public function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
            ->addItem('Nos réalisations', $this->generateUrl('app_achieve_index'));

        return $breadcrumbs;
    }
}
