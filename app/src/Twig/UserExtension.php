<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            // new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userFullname', [$this, 'userAvailableName']),
        ];
    }

    public function userAvailableName(User $user)
    {
        if(!empty($user->getFirstname()) && !empty($user->getLastname())) {
            return $user->getFirstname() . ' ' . $user->getLastname();
        }
        return $user->getEmail();
    }
}
