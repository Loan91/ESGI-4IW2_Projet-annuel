<?php

namespace App\Controller\Front;

use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Form\PropertyType;


class PropertyController extends AbstractController
{


    /**
     * @Route("/property", name="property_index", methods={"GET"})
     */
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('front/property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/property/new", name="property_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $property = new Property();
        $property->setOwner($this->getUser());
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($property);
            $entityManager->flush();

            return $this->redirectToRoute('front_property_index');
        }

        return $this->render('front/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/property/{id}", name="property_show", methods={"GET"})
     */
    public function show(Property $property): Response
    {
        return $this->render('front/property/show.html.twig', [
            'property' => $property,
        ]);
    }

    /**
     * @Route("/property/{id}/edit", name="property_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Property $property): Response
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('front_property_index');
        }

        return $this->render('front/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/property/{id}", name="property_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Property $property): Response
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($property);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_property_index');
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
