<?php

namespace App\Controller\Account;

use App\Repository\UserRepository;
use App\Entity\EmailVerification;
use App\Service\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class EmailChangeController extends AbstractController
{
    public function confirm(
        EmailVerification $emailVerification,
        ProfileService $service,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response
    {
        if ($emailVerification->isExpired()) {
            $this->addFlash('error', 'Cette demande de confirmation a expiré');
        } else {
            $user = $userRepository->findOneByEmail($emailVerification->getEmail());

            // Un utilisateur existe déjà avec cet email
            if ($user) {
                $this->addFlash('error', 'Cet email est déjà utilisé');

                return $this->redirectToRoute('app_login');
            }

            $service->updateEmail($emailVerification);
            $em->flush();
            $this->addFlash('success', 'Votre email a bien été modifié');
        }

        return $this->redirectToRoute('app_user_profil_edit');
    }
}
