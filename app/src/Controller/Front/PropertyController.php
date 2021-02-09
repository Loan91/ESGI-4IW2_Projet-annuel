<?php

namespace App\Controller\Front;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
  /**
   * @Route("/propriete", name="front_property")
   * @param PropertyRepository $repository
   * @param Request $request
   * @return Response
   */
    public function index(PropertyRepository $repository, Request $request): Response
    {
      $data = new SearchData();

      $form = $this->createForm(SearchType::class, $data);
      $form->handleRequest($request);

      #dd($data);

      if ($form->isSubmitted() && $form->isValid()) {
        $properties = $repository->findSearch($data);
      } else {
        $properties = $repository->findAll();
      }

      return $this->render('front/property/index.html.twig', [
            'properties' => $properties,
            'form' => $form->createView(),
        ]);
    }
}
