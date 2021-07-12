<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Security\Voter\ContactVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/account/contacts", name="contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front/contact/index.html.twig', [
            'contacts' => $contactRepository->getContactsOrdered($user->getId()),
        ]);
    }

    /**
     * @Route("/account/my-contacts", name="contact_mycontacts", methods={"GET"})
     */
    public function getMyContacts(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front/contact/mycontacts.html.twig', [
            'contacts' => $contactRepository->getMyContactsOrdered($user->getId()),
        ]);
    }

    /**
     * @Route("/contact/new/{id}", name="contact_create", methods={"GET","POST"})
     * @param Property $property Property for the contact
     */
    public function new(Request $request, Property $property): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::NEW_CONTACT, $property);

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

            $this->addFlash('success', 'Votre demande de RDV a été envoyé !');
            return $this->redirectToRoute('front_contact_mycontacts');
        }

        return $this->render('front/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contact/accept/{id}", name="contact_acceptDate", methods={"GET"})
     * @param Contact $contact Contact to accept
     */
    public function acceptDate(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        $contact->setStatus('RDV_VALIDE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Date de RDV accepté !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/another/{id}", name="contact_anotherDate", methods={"GET"})
     * @param Contact $contact Contact to ask another date
     */
    public function anotherDate(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        $contact->setStatus('RDV_NOUVELLE_DATE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Demande de nouvelle date de RDV envoyée !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/decline/{id}", name="contact_decline", methods={"GET"})
     * @param Contact $contact Contact to decline
     */
    public function decline(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        $contact->setStatus('RDV_FERME');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->addFlash('success', 'RDV fermé !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/finish/{id}", name="contact_finish", methods={"GET"})
     * @param Contact $contact Contact to finish
     */
    public function finish(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        $contact->setStatus('RDV_TERMINE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->addFlash('success', 'RDV marqué comme terminé !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/new_date/{id}", name="contact_new_date", methods={"POST"})
     * @param Contact $contact Contact to choose new date
     */
    public function newDate(Contact $contact): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::PROSPECT_CONTACT, $contact);

        $contact->setStatus('RDV_CREE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Nouvelle date de RDV envoyée !');
        return $this->redirectToRoute('front_contact_index');
    }

}
