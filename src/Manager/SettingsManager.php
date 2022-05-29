<?php

namespace App\Manager;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SettingsManager
 * @package App\Manager
 */
class SettingsManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * SettingsManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get()
    {
        return $this->em->getRepository(Settings::class)->getSettings();
    }
}