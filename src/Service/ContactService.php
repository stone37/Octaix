<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Exception\TooManyContactException;
use App\Model\ContactData;
use App\Repository\ContactRequestRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ContactService
{
    private $repository;
    private $em;
    private $mailer;

    public function __construct(
        ContactRequestRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * @param ContactData $data
     * @param Request $request
     * @throws TooManyContactException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(ContactData $data, Request $request): void
    {
        $contactRequest = (new ContactRequest())->setRawIp($request->getClientIp());
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

        $message = (new Email())
            ->text($data->content)
            ->subject("Octaix::Contact : {$data->name} ({$data->phone})")
            ->from('noreply@octaix.com')
            ->replyTo(new Address($data->email, $data->name))
            ->to('contact@octaix.com');

        $this->mailer->send($message);
    }
}
