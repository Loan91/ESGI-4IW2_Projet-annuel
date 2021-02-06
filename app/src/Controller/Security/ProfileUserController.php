<?php

namespace App\Controller\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
