<?php

namespace App\Service;

use App\Dto\AvatarDto;
use App\Dto\ProfileUpdateDto;
use App\Entity\EmailVerification;
use App\Event\EmailVerificationEvent;
use App\Exception\TooManyEmailChangeException;
use App\Repository\EmailVerificationRepository;
use App\Security\TokenGeneratorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileService
{
    private $tokenGeneratorService;
    private $emailVerificationRepository;
    private $dispatcher;
    private $em;

    public function __construct(
        TokenGeneratorService $tokenGeneratorService,
        EmailVerificationRepository $emailVerificationRepository,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    public function updateAvatar(AvatarDto $data): void
    {
        if (false === $data->file->getRealPath()) {
            throw new RuntimeException('Impossible de redimensionner un avatar non existant');
        }
        // On redimensionne l'image
        $manager = new ImageManager(['driver' => 'imagick']);
        $manager->make($data->file)->fit(110, 110)->save($data->file->getRealPath());

        // On la déplace dans le profil utilisateur
        $data->user->setFile($data->file);
        $data->user->setUpdatedAt(new DateTime());
    }

    /**
     * @param ProfileUpdateDto $data
     * @throws TooManyEmailChangeException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function updateProfile(ProfileUpdateDto $data): void
    {
        $data->user->setUsername($data->username);
        $data->user->setLastName($data->lastName);
        $data->user->setFirstName($data->firstName);
        $data->user->setPhone($data->phone);
        $data->user->setPhoneStatus($data->phoneStatus);
        $data->user->setAddress($data->address);
        $data->user->setCity($data->city);

        if ($data->email !== $data->user->getEmail()) {
            $lastRequest = $this->emailVerificationRepository->findLastForUser($data->user);

            if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('-1 hour')) {
                throw new TooManyEmailChangeException($lastRequest);
            } else {
                if ($lastRequest) {
                    $this->em->remove($lastRequest);
                }
            }

            $emailVerification = (new EmailVerification())
                ->setEmail($data->email)
                ->setAuthor($data->user)
                ->setCreatedAt(new DateTime())
                ->setToken($this->tokenGeneratorService->generate());

            $this->em->persist($emailVerification);

            $this->dispatcher->dispatch(new EmailVerificationEvent($emailVerification));
        }
    }

    public function updateEmail(EmailVerification $emailVerification): void
    {
        $emailVerification->getAuthor()->setEmail($emailVerification->getEmail());

        $this->em->remove($emailVerification);
    }
}
