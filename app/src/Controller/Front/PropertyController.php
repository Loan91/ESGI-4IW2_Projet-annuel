<?php

namespace App\Controller\Front;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Form\PropertyType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/properties")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="property_index", methods={"GET"})
     */
    public function index(PropertyRepository $propertyRepository, TokenStorageInterface $tokenStorage): Response
    {
        // Utilisateur courant
        $user = $tokenStorage->getToken()->getUser();

        return $this->render('front/property/index.html.twig', [
            'properties' => $propertyRepository->getUserProperties($user)
        ]);
    }

    /**
     * @Route("/new", name="property_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $property = new Property();
        $property->setOwner($this->getUser());
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $property->addImage($img);
            }

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
     * @Route("/{id}", name="property_show", methods={"GET"})
     */
    public function show(Property $property): Response
    {
        return $this->render('front/property/show.html.twig', [
            'property' => $property,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="property_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Property $property): Response
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $property->addImage($img);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('front_property_index');
        }

        return $this->render('front/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="property_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Property $property): Response
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($property->getImages() as $image){
                $entityManager->remove($image);
            }
            $entityManager->remove($property);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_property_index');
    }
}
