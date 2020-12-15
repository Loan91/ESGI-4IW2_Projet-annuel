<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/back", name="default_index")
     */
    public function index(): Response
    {
        return $this->render('Back/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
