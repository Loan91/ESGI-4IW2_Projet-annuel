<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * @Route("/about-us", name="about_us", methods={"GET"})
     */
    public function aboutUs(): Response
    {
        return $this->render('front/about-us.html.twig');
    }
}
