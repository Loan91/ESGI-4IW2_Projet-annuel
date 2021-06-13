<?php

namespace App\Controller\Front;

use App\Entity\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
  /**
   * @Route("/save_search", name="save_search")
   * @param Request $request
   * @return Response
   */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $search = new Search();
        $search->setSearcher($this->getUser());
        $search->setType($request->query->get('type'));
        $search->setCategory($request->query->get('category'));
        $search->setCity($request->query->get('city'));
        $search->setMinPrice($request->query->get('minPrice'));
        $search->setMaxPrice($request->query->get('maxPrice'));

        $em->persist($search);
        $em->flush();

        $this->addFlash('success', 'Votre recherche à bien été sauvegardé !');

        return $this->redirectToRoute('front_annonce_index', $request->query->all());
    }
}
