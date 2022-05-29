<?php

namespace App\Authenticator;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Exception\EmailAlreadyUsedException;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use RuntimeException;

class FacebookAuthenticator extends AbstractSocialAuthenticator
{
    protected $serviceName = 'facebook';

    /**
     * @param ResourceOwnerInterface $facebookUser
     * @param UserRepository $repository
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserFromResourceOwner(ResourceOwnerInterface $facebookUser, UserRepository $repository): ?User
    {
        if (!($facebookUser instanceof FacebookUser)) {
            throw new RuntimeException('Expecting FacebookClient as the first parameter');
        }
        $user = $repository->findForOauth('facebook', $facebookUser->getId(), $facebookUser->getEmail());
        if ($user && null === $user->getFacebookId()) {
            throw new EmailAlreadyUsedException();
        }

        return $user;
    }
}
