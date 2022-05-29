<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\PasswordResetConfirmData;
use App\Data\PasswordResetRequestData;
use App\Entity\PasswordResetToken;
use App\Form\PasswordResetConfirmForm;
use App\Form\PasswordResetRequestForm;
use App\Service\PasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class PasswordController extends AbstractController
{
    /**
     * @param Request $request
     * @param PasswordService $resetService
     * @return Response
     * @throws \Exception
     */
    public function reset(Request $request, PasswordService $resetService): Response
    {
        $error = null;
        $data = new PasswordResetRequestData();

        $form = $this->createForm(PasswordResetRequestForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $resetService->resetPassword($form->getData());
                $this->addFlash('success', 'Les instructions pour réinitialiser votre mot de passe vous ont été envoyées');

                return $this->redirectToRoute('app_login');
            } catch (AuthenticationException $e) {
                /** @var AuthenticationException $error */
                $error = $e;
            }
        }

        return $this->render('site/password/password_reset.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param PasswordResetToken|null $token
     * @param PasswordService $service
     * @return Response
     * @throws \Exception
     */
    public function confirm(
        Request $request,
        User $user,
        ?PasswordResetToken $token,
        PasswordService $service): Response
    {
        if (!$token || $service->isExpired($token) || $token->getUser() !== $user) {
            $this->addFlash('error', 'Ce token a expiré');

            return $this->redirectToRoute('app_login');
        }

        $error = null;
        $data = new PasswordResetConfirmData();

        $form = $this->createForm(PasswordResetConfirmForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->updatePassword($data->getPassword(), $token);
            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('site/password/password_reset_confirm.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}
