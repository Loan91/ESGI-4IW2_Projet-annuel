<?php

namespace App\Controller\Front;

use App\Repository\PropertyRepository;
use App\Repository\SearchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Form\OwnedPropertiesSearchType;
use App\Form\PropertyType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Security\Voter\OwnedPropertyVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/properties")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="property_index", methods={"GET"})
     */
    public function index(Request $request, PropertyRepository $propertyRepository): Response
    {
        $form = $this->createForm(OwnedPropertiesSearchType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $paginator = $propertyRepository->getPropertiesPaginationForUserByCity($this->getUser(), $request, $formData['city'], 4);
        } else {
            $paginator = $propertyRepository->getPropertiesPaginationForUser($this->getUser(), $request, 4);
        }

        foreach ($paginator as $property) {
            $this->denyAccessUnlessGranted(OwnedPropertyVoter::VIEW, $property);
        }

        return $this->render('front/property/index.html.twig', [
            'paginator' => $paginator,
            'searchForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/recherches-sauvegardees", name="property_saved_search", methods={"GET"})
     * @param Request $request
     * @param SearchRepository $searchRepository
     * @return Response
     */
    public function saved_search(Request $request, SearchRepository $searchRepository): Response
    {
        $saved_searches = $searchRepository->findSavedSearchByUser();

        return $this->render('front/property/saved-search.html.twig', [
            'saved_searches' => $saved_searches,
        ]);
    }


    /**
     * @Route("/new", name="property_new", methods={"GET","POST"})
     * @param Request $request
     * @param SearchRepository $searchRepository
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function new(Request $request, SearchRepository $searchRepository, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted(OwnedPropertyVoter::CREATE, Property::class);

        $property = new Property();
        $property->setOwner($this->getUser());
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($property);
            $entityManager->flush();

            $users = $searchRepository->findInterestedUsers($property);

            $email = (new TemplatedEmail())
                ->from('noreply@sinequanone.com')
                ->subject('Nouveau bien disponible')
                ->htmlTemplate('emails/available-property.html.twig')
            ;

            foreach ($users as $user)
            {
                $email->to($user['email']);
                $email->context([
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                ]);
                $mailer->send($email);
            }

            $this->addFlash('success', 'Votre nouveau bien à ' . $property->getCity() . ' s\'est ajouté correctement');
            return $this->redirectToRoute('front_property_index');
        }

        return $this->render('front/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="property_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Property $property): Response
    {
        $this->denyAccessUnlessGranted(OwnedPropertyVoter::UPDATE, $property);

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le bien au dossier ' . $property->getId() . ' à ' . $property->getCity() . ' s\'est mis à jour correctement');
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
        $this->denyAccessUnlessGranted(OwnedPropertyVoter::DELETE, $property);

        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($property);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Le bien qui se situait à ' . $property->getCity() . ' a été correctement supprimé');
        return $this->redirectToRoute('front_property_index');
    }
}
