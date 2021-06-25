<?php

namespace App\Security\Voter;

use App\Entity\Property;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OwnedPropertyVoter extends Voter
{
    const VIEW = 'view_own_property';
    const CREATE = 'create_own_property';
    const UPDATE = 'update_own_property';
    const DELETE = 'delete_own_property';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::CREATE])
            && $subject == \App\Entity\Property::class)
            || (in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])
                && $subject instanceof \App\Entity\Property);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var \App\Entity\User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Atteste que l'utilisateur est membre ou admin
        if ($user->hasNotRole('ROLE_USER') && $user->hasNotRole('ROLE_ADMIN')) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::CREATE:
                return $this->canCreate($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        return false;
    }

    public function canView(Property $property, User $user): bool
    {
        if ($property->getOwner()->getId() !== $user->getId()) {
            return false;
        }
        return true;
    }

    public function canCreate(string $property, User $user): bool
    {
        // L'utilisateur a le rôle membre, il peut créer un article
        return true;
    }

    public function canUpdate(Property $property, User $user): bool
    {
        // Si l'utilisateur peut voir, il peut également éditer
        return $this->canView($property, $user);
    }

    public function canDelete(Property $property, User $user): bool
    {
        // Si l'utilisateur peut voir, il peut également supprimer
        return $this->canView($property, $user);
    }
}
