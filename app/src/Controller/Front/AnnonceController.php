<?php

namespace App\Controller\Front;

use App\Data\SearchData;
use App\Entity\Property;
use App\Form\SearchType;
use App\Repository\FavoriteRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/nos-annonces", name="annonce_index")
     * @param PropertyRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(PropertyRepository $repository, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data);

        if($request->query->has('ville')) {
            $city = $request->query->get('ville');
            $data->city = $city;
            $properties = $repository->findSearch($data);
        } else {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
              $properties = $repository->findSearch($data);
            } else {
              $properties = $repository->findAll();
            }
        }

        return $this->render('front/annonce/index.html.twig', [
            'properties' => $properties,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/nos-annonces/{id}", name="annonce_show", methods={"GET"})
     * @param Property $property
     * @return mixed
     */
    public function show(Property $property, FavoriteRepository $repository)
    {
        $isFav = $repository->isFavorite($property, $this->getUser());
        return $this->render('front/annonce/show.html.twig', [
            'property' => $property,
            'isFav' => $isFav,
        ]);
    }
}
