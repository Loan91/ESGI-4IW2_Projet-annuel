<?php

namespace App\Controller\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileUserController extends AbstractController
{

    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig');
    }
}
