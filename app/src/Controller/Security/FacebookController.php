<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    /**
     * @Route("/security/facebook", name="security_facebook")
     */
    public function index(): Response
    {
        return $this->render('security/facebook/index.html.twig', [
            'controller_name' => 'FacebookController',
        ]);
    }
}
