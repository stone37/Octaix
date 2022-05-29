<?php

namespace App\Security;

use App\Entity\Advert;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAdvertVoter extends Voter
{
    // these strings are just invented: you can use anything
    const CREATE = 'create';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::CREATE])) {
            return false;
        }

        if (!$subject instanceof Advert) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Advert $advert */
        $advert = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($advert, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canCreate(Advert $advert, User $user)
    {
        return $user === $advert->getUser();
    }
}
