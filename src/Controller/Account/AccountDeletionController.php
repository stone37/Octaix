<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Manager\SettingsManager;
use App\Service\DeleteAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AccountDeletionController extends AbstractController
{

    private $settings;

    public function __construct(SettingsManager $manager) {
        $this->settings = $manager->get();
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param DeleteAccountService $service
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function delete(
        Request $request,
        DeleteAccountService $service,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response
    {
        $user = $this->getUserOrThrow();

        $form = $this->deleteForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if (!$passwordEncoder->isPasswordValid($user, $data['password'] ?? '')) {
                    $this->addFlash('error', 'Impossible de supprimer le compte, mot de passe invalide');

                    return $this->redirectToRoute('app_user_delete');
                }

                $service->deleteUser($user, $request);

                $this->addFlash('error', 'Votre demande de suppression de compte a bien été prise en compte. 
            Votre compte sera supprimé automatiquement au bout de '.DeleteAccountService::DAYS.' jours');

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('user/account/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'settings' => $this->settings
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @param DeleteAccountService $service
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteAjax(
        DeleteAccountService $service,
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request
    ): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('delete-account', $data['csrf'] ?? '')) {
            return new JsonResponse([
                'title' => 'Token CSRF invalide',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$passwordEncoder->isPasswordValid($user, $data['password'] ?? '')) {
            return new JsonResponse([
                'title' => 'Impossible de supprimer le compte, mot de passe invalide',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $service->deleteUser($user, $request);

        return new JsonResponse([
            'message' => 'Votre demande de suppression de compte a bien été prise en compte. 
            Votre compte sera supprimé automatiquement au bout de '.DeleteAccountService::DAYS.' jours',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function cancelDelete(EntityManagerInterface $em): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setDeleteAt(null);

        $em->flush();

        $this->addFlash('success', 'La suppression de votre compte a bien été annulée');

        return $this->redirectToRoute('app_user_delete');
    }

    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    private function deleteForm()
    {
        return $this->createFormBuilder([])
            ->setAction($this->generateUrl('app_user_delete'))
            ->setMethod('DELETE')
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'Entrez votre mot de passer pour confirmer']])
            ->getForm();
    }
}
