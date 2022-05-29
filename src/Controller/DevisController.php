<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Exception\TooManyContactException;
use App\Form\DevisType;
use App\Model\DevisData;
use App\Service\DevisService;
use App\Service\SettingsManager;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DevisController extends AbstractController
{
    use ControllerTrait;

    private $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function index(
        Request $request,
        Breadcrumbs $breadcrumbs,
        DevisService $devisService/*,
        ReCaptcha $reCaptcha*/)
    {
        $this->breadcrumb($breadcrumbs)->addItem('Demande de devis');

        $data = new DevisData();
        $form = $this->createForm(DevisType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true/*$reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()*/) {
                try {
                    $devisService->send($data, $request);
                } catch (TooManyContactException $exception) {
                    $this->addFlash('error', 'Vous avez fait trop de demandes de contact consécutives.');

                    return $this->redirectToRoute('app_contact');
                }

                $this->addFlash('success', 'Votre demande de contact a été transmis, nous vous répondrons dans les meilleurs délais.');

                return $this->redirectToRoute('app_contact');
            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre message');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('site/devis/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $this->settings,
            'data' => $data,
        ]);
    }
}

