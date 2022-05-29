<?php

namespace App\Service;

use App\Entity\DevisRequest;
use App\Exception\TooManyContactException;
use App\Mailing\Mailer;
use App\Model\DevisData;
use App\Repository\DevisRequestRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class DevisService
{
    private $repository;
    private $em;
    private $mailer;

    public function __construct(
        DevisRequestRepository $repository,
        EntityManagerInterface $em,
        Mailer $mailer
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @param DevisData $data
     * @param Request $request
     * @throws TooManyContactException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(DevisData $data, Request $request): void
    {
        $contactRequest = (new DevisRequest())->setRawIp($request->getClientIp());
        $lastRequest = $this->repository->findLastRequestForIp($contactRequest->getIp());

        if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('- 1 hour')) {
            throw new TooManyContactException();
        }

        if (null !== $lastRequest) {
            $lastRequest->setCreatedAt(new DateTime());
        } else {
            $this->em->persist($contactRequest);
        }

        $this->em->flush();

        $email = $this->mailer->createEmail('mails/data/devis.twig', ['data' => $data])
                    ->to('contact@octaix.com')
                    ->subject("Octaix::Devis de : {$data->name}");
        $this->mailer->sendNow($email);
    }
}
