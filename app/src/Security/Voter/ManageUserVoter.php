<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ManageUserVoter extends Voter
{
    const VIEW = 'view_user';
    const CREATE = 'create_user';
    const UPDATE = 'update_user';
    const DELETE = 'delete_user';
    const TOGGLE_STATUS = 'toggle_status_user';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::CREATE]) && $subject == \App\Entity\User::class)
            || (in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE, self::TOGGLE_STATUS]) && $subject instanceof \App\Entity\User);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var \App\Entity\User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // The user must be admin
        if ($user->hasNotRole('ROLE_ADMIN')) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            // Can see your own account or create a new one
            case self::VIEW:
            case self::CREATE:
                return true;
            case self::UPDATE:
            case self::DELETE:
            case self::TOGGLE_STATUS:
                // Cannot delete/update your own account
                if ($subject->getId() === $user->getId()) {
                    return false;
                }
                return true;
        }

        return false;
    }
}
