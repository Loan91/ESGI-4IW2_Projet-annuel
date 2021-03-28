<?php

namespace App\Controller\Front;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * @Route("/search", name="property_search")
     * @param PropertyRepository $repository
     * @param Request $request
     * @return Response
     */
    public function search(PropertyRepository $repository, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $properties = $repository->findSearch($data);
        } else {
            $properties = $repository->findAll();
        }

        return $this->render('front/property/search.html.twig', [
            'properties' => $properties,
            'form' => $form->createView(),
        ]);
    }
}
