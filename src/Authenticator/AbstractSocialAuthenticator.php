<?php

namespace App\Authenticator;

use App\Auth\AuthService;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Exception\UserAuthenticatedException;
use App\Exception\UserOauthNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractSocialAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    protected $serviceName = '';
    private $clientRegistry;
    protected $em;
    private $router;
    private $authService;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        AuthService $authService
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->authService = $authService;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function supports(Request $request): bool
    {
        if ('' === $this->serviceName) {
            throw new Exception("You must set a \$serviceName property (for instance 'github', 'facebook')");
        }

        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === $this->serviceName;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function getCredentials(Request $request): AccessTokenInterface
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @param AccessToken $credentials
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        $resourceOwner = $this->getResourceOwnerFromCredentials($credentials);
        $user = $this->authService->getUserOrNull();
        if ($user) {
            throw new UserAuthenticatedException($user, $resourceOwner);
        }
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $user = $this->getUserFromResourceOwner($resourceOwner, $repository);
        if (null === $user) {
            throw new UserOauthNotFoundException($resourceOwner);
        }

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        if ($exception instanceof UserOauthNotFoundException) {
            return new RedirectResponse($this->router->generate('app_register', ['oauth' => 1]));
        }

        if ($exception instanceof UserAuthenticatedException) {
            return new RedirectResponse($this->router->generate('app_home'));
        }

        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        // On force le remember me pour déclencher le AbstractRememberMeServices (en attendant mieux)
        $request->request->set('_remember_me', '1');

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User
    {
        return null;
    }

    protected function getClient(): OAuth2Client
    {
        /** @var OAuth2Client $client */
        $client = $this->clientRegistry->getClient($this->serviceName);

        return $client;
    }
}
