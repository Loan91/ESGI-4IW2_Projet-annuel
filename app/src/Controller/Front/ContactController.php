<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/compte/rendez-vous", name="contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front/contact/index.html.twig', [
            'contacts' => $contactRepository->getContactsOrdered($user->getId()),
        ]);
    }

    /**
     * @Route("/compte/rendez-vous/demandes", name="contact_mycontacts", methods={"GET"})
     */
    public function getMyContacts(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front/contact/mycontacts.html.twig', [
            'contacts' => $contactRepository->getMyContactsOrdered($user->getId()),
        ]);
    }

    /**
     * @Route("/compte/rendez-vous/nouveau/{id}", name="contact_create", methods={"GET","POST"})
     * @param Property $property Property for the contact
     */
    public function new(Request $request, Property $property): Response
    {
        $contact = new Contact();
        $contact->setProspect($this->getUser());
        $contact->setProperty($property);
        $contact->setStatus('RDV_CREE');
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('front_contact_mycontacts');
        }

        return $this->render('front/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
