<?php

namespace App\Controller;

use App\Dto\ProfileUpdateDto;
use App\Entity\User;
use App\Exception\TooManyEmailChangeException;
use App\Service\ProfileService;
use App\Form\UpdatePasswordForm;
use App\Form\UpdateProfileForm;
use App\Service\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends AbstractController
{
    private $passwordEncoder;
    private $em;
    private $profileService;
    private $settings;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        ProfileService $profileService,
        SettingsManager $manager
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->profileService = $profileService;
        $this->settings = $manager->get();
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request): Response {

        $user = $this->getUserOrThrow();

        $formUpdate = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        $formUpdate->handleRequest($request);

        try {
            if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
                $data = $formUpdate->getData();
                $this->profileService->updateProfile($data);
                $this->em->flush();

                if ($user->getEmail() !== $data->email) {
                    $this->addFlash(
                        'info',
                        "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                    );
                } else {
                    $this->addFlash('success', 'Votre profil a bien été mis à jour');
                }

                return $this->redirectToRoute('app_user_profil_edit');
            }
        } catch (TooManyEmailChangeException $e) {
            $this->addFlash('error', "Vous avez déjà un changement d'email en cours.");
        }

        return $this->render('user/account/edit.html.twig', [
            'form_update' => $formUpdate->createView(),
            'user' => $user,
            'settings' => $this->settings
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function changePassword(Request $request): Response
    {
        $user = $this->getUserOrThrow();
        $form = $this->createForm(UpdatePasswordForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
            $this->em->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('app_user_change_password');
        }

        return $this->render('user/account/change_password.html.twig', [
            'form_password' => $form->createView(),
            'user' => $user,
            'settings' => $this->settings
        ]);
    }

    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}
