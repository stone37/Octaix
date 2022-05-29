<?php

namespace App\EventListener;

use App\Exception\UserAuthenticatedException;
use App\Exception\UserOauthNotFoundException;
use App\Service\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthenticationFailureListener implements EventSubscriberInterface
{
    private $normalizer;
    private $em;
    private $session;

    public function __construct(NormalizerInterface $normalizer, SessionInterface $session, EntityManagerInterface $em)
    {
        $this->normalizer = $normalizer;
        $this->em = $em;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * @param AuthenticationFailureEvent $event
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getAuthenticationException();
        if ($exception instanceof UserOauthNotFoundException) {
            $this->onUserNotFound($exception);
        }
        if ($exception instanceof UserAuthenticatedException) {
            $this->onUserAlreadyAuthenticated($exception);
        }
    }

    /**
     * @param UserOauthNotFoundException $exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onUserNotFound(UserOauthNotFoundException $exception): void
    {
        $data = $this->normalizer->normalize($exception->getResourceOwner());
        $this->session->set(SocialLoginService::SESSION_KEY, $data);
    }

    /**
     * @param UserAuthenticatedException $exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onUserAlreadyAuthenticated(UserAuthenticatedException $exception): void
    {
        $resourceOwner = $exception->getResourceOwner();
        $user = $exception->getUser();
        /** @var array{type: string} $data */
        $data = $this->normalizer->normalize($exception->getResourceOwner());
        $setter = 'set'.ucfirst($data['type']).'Id';
        $user->$setter($resourceOwner->getId());
        $this->em->flush();
        if ($this->session instanceof Session) {
            $this->session->getFlashBag()->set('success', 'Votre compte a bien été associé à '.$data['type']);
        }
    }
}
