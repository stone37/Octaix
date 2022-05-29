<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Settings;
use App\Exception\TooManyContactException;
use App\Model\ContactData;
use App\Service\ContactService;
use App\Service\SettingsManager;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Form\ContactType;

class ContactController extends AbstractController
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

    public function index(
        Request $request,
        Breadcrumbs $breadcrumbs,
        ContactService $contactService/*, ReCaptcha $reCaptcha*/)
    {
        $this->breadcrumb($breadcrumbs)->addItem('Contact');

        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true/*$reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()*/) {
                try {
                    $contactService->send($data, $request);
                } catch (TooManyContactException $exception) {
                    $this->addFlash('error', 'Vous avez fait trop de demandes de contact consécutives.');

                    return $this->redirectToRoute('app_contact');
                }

                $this->addFlash('success', 'Votre demande de contact a été transmis, nous vous répondrons dans les meilleurs délais.');

                return $this->redirectToRoute('app_contact');
            } else {
                //$this->addFlash('error', 'Erreur pendant l\'envoi de votre message');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('site/contact/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $this->settings,
        ]);
    }
}

