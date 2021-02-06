<?php

namespace App\Controller\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileUserController extends AbstractController
{

    /**
     * @Route("/users", name="app_users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig');
    }
}
