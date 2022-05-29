<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\NewsletterType;
use App\Mailing\Mailer;
use App\Service\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class NewsletterController extends AbstractController
{
    private $settings;

    public function __construct(SettingsManager $settings)
    {
        $this->settings = $settings->get();
    }

    public function index(Request $request, Mailer $mailer, EntityManagerInterface $em)
    {
        $emails = $em->getRepository(User::class)->findCustomerEmails();

        $form = $this->createForm(NewsletterType::class, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            foreach ($emails as $data) {
                $sender = $mailer->createEmail('mails/newsletter/pro.twig', [
                    'data' => $formData,
                ])->to($data['email'])
                    ->subject($this->settings->getName().' | '.$formData['subject']);

                $mailer->send($sender);
            }

            $this->addFlash('info', 'Newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_newsletter_index');
        }

        return $this->render('admin/newsletter/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

