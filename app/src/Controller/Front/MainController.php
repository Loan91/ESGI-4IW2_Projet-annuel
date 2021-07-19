<?php

namespace App\Controller\Front;

use App\Service\Mailer;
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

    /**
     * @Route("/privacy-policy", name="privacy_policy", methods={"GET"})
     */
    public function privacyPolicy(): Response
    {
        return $this->render('front/politique-confidentialite.html.twig');
    }

    /**
     * @Route("/cgu", name="cgu", methods={"GET"})
     */
    public function cgu(): Response
    {
        return $this->render('front/cgu.html.twig');
    }



}
