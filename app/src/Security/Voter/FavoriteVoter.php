<?php

namespace App\Security\Voter;

use App\Entity\Favorite;
use App\Entity\Property;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class FavoriteVoter extends Voter
{
    const CREATE_FAVORITE = "create_favorite";
    const DELETE_FAVORITE = "delete_favorite";
    const IS_LOGGED       = "is_logged";

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return 
            (in_array($attribute, [self::CREATE_FAVORITE])
                && $subject instanceof \App\Entity\Property)
            || (in_array($attribute, [self::DELETE_FAVORITE])
                && $subject instanceof \App\Entity\Favorite)
            || in_array($attribute, [self::IS_LOGGED])
            ;
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
            case self::CREATE_FAVORITE:
                return $this->canCreate($subject, $user);
            case self::DELETE_FAVORITE:
                return $this->canDelete($subject, $user);
            case self::IS_LOGGED:
                return true;
        }

        return false;
    }

    public function canCreate(Property $property, User $user): bool
    {
        // If owner and prospect had the same id
        if ($property->getOwner()->getId() === $user->getId()) {
            return false;
        }

        foreach ($user->getFavorites() as $favorite)
        {
            if ($favorite->getProperty() === $property)
            {
                return false;
            }
        }

        return true;
    }

    public function canDelete(Favorite $favorite, User $user): bool
    {
        // If favorite user and current user had the same id
        if ($favorite->getUser()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }
}
