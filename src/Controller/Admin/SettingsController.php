<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Form\SettingsModuleType;
use App\Form\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class SettingsController extends AbstractController
{
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $settings = $em->getRepository(Settings::class)->getSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($settings);
            $em->flush();

            $this->addFlash('info', 'Les paramètres du site ont été mise à jour');

            return $this->redirectToRoute('app_admin_settings_index');
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    public function meta(Request $request, EntityManagerInterface $em): Response
    {
        $settings = $em->getRepository(Settings::class)->getSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($settings);
            $em->flush();

            $this->addFlash('info', 'Les paramètres meta tags ont été mise à jour');

            return $this->redirectToRoute('app_admin_settings_meta_tags_index');
        }

        return $this->render('admin/settings/meta.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    public function module(Request $request, EntityManagerInterface $em)
    {
        $settings = $em->getRepository(Settings::class)->getSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsModuleType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($settings);
            $em->flush();

            $this->addFlash('info', 'Les paramètres des modules ont été mise à jour');

            return $this->redirectToRoute('app_admin_settings_module_index');
        }

        return $this->render('admin/settings/module_site.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }
}

