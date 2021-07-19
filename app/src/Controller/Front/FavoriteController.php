<?php

namespace App\Controller\Front;

use App\Entity\Favorite;
use App\Entity\Property;
use App\Repository\FavoriteRepository;
use App\Security\Voter\FavoriteVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    /**
     * @Route("/favorite", name="favorite_index")
     */
    public function index(FavoriteRepository $repository): Response
    {
        $this->denyAccessUnlessGranted(FavoriteVoter::IS_LOGGED);

        $user = $this->getUser();

        return $this->render('front/favorite/index.html.twig', [
            'favorites' => $repository->getFavorites($user)
        ]);
    }

    /**
     * @Route("/favorite/new/{id}", name="favorite_create", methods={"GET"})
     * @param Property $property Property for the favorite
     */
    public function new(Request $request, Property $property, FavoriteRepository $repository): Response
    {
        $this->denyAccessUnlessGranted(FavoriteVoter::CREATE_FAVORITE, $property);

        $favorite = new Favorite();
        $favorite->setProperty($property);
        $favorite->setUser($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($favorite);
        $entityManager->flush();

        $this->addFlash('success', 'Annonce ajoutée à vos favoris !');
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/favorite/delete/{id}", name="favorite_delete", methods={"GET"})
     * @param Favorite $favorite to delete
     */
    public function delete(Request $request, Favorite $favorite): Response
    {
        $this->denyAccessUnlessGranted(FavoriteVoter::DELETE_FAVORITE, $favorite);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($favorite);
        $entityManager->flush();

        $this->addFlash('success', 'Annonce supprimée de vos favoris !');
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
