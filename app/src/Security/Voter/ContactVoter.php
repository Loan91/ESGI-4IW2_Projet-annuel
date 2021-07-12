<?php

namespace App\Security\Voter;

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactVoter extends Voter
{
    const NEW_CONTACT      = 'create_new_contact';
    const OWNER_CONTACT    = 'owner_modify_contact';
    const PROSPECT_CONTACT = 'prospect_modify_contact';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return
            (in_array($attribute, [self::NEW_CONTACT])
                && $subject instanceof \App\Entity\Property)
            || (in_array($attribute, [self::OWNER_CONTACT, self::PROSPECT_CONTACT])
                && $subject instanceof \App\Entity\Contact);
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
            case self::NEW_CONTACT:
                return $this->canCreate($subject, $user);
            case self::OWNER_CONTACT:
                return $this->propertyOwner($subject, $user);
            case self::PROSPECT_CONTACT:
                return $this->propertyProspect($subject, $user);
        }

        return false;
    }

    public function canCreate(Property $property, User $user): bool
    {
        // If owner and prospect had the same id
        if ($property->getOwner()->getId() === $user->getId()) {
            return false;
        }
        return true;
    }

    public function propertyOwner(Contact $contact, User $user): bool
    {
        // If user is the property owner
        if ($contact->getProperty()->getOwner()->getId() !== $user->getId()) {
            return false;
        }
        return true;
    }

    public function propertyProspect(Contact $contact, User $user): bool
    {
        // If user is the property prospect
        if ($contact->getProperty()->getOwner()->getId() !== $user->getId()) {
            return false;
        }
        return true;
    }
}
