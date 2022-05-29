<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Advert;
use App\Entity\Settings;
use App\Model\Search;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DashboardController
 * @package App\Controller
 */
class DashboardController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function index(EntityManagerInterface $em)
    {
        $search = (new Search())->setUser($this->getUser());

        return $this->render('user/dashboard/index.html.twig', [
            'settings' => $this->settings,
            'user'     => $this->getUsers($em, $this->getUser()->getId()),
            'advertAN' => $em->getRepository(Advert::class)->getUserAdvertActiveNumber($search),
        ]);
    }
}
