<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilVoter extends Voter
{
    const VIEW = 'view_own_profil';
    const UPDATE = 'update_own_profil';
    const DELETE = 'delete_own_profil';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $subject);
            case self::UPDATE:
                return $this->canUpdate($user, $subject);
            case self::DELETE:
                return $this->canDelete($user, $subject);
        }

        return false;
    }

    public function canView(User $currentUser, User $intentedUser)
    {
        return $currentUser->getId() == $intentedUser->getId();
    }

    public function canUpdate(User $currentUser, User $intentedUser)
    {
        return $currentUser->getId() == $intentedUser->getId();
    }

    public function canDelete(User $currentUser, User $intentedUser)
    {
        if ($currentUser->hasRole('ROLE_ADMIN')) {
            return false;
        }
        return true;
    }
}
