<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Security\Voter\ContactVoter;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/account/contacts", name="contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::IS_LOGGED);
     
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
        $this->denyAccessUnlessGranted(ContactVoter::IS_LOGGED);
        
        $user = $this->getUser();
        return $this->render('front/contact/mycontacts.html.twig', [
            'contacts' => $contactRepository->getMyContactsOrdered($user->getId()),
        ]);
    }

    /**
     * @Route("/contact/new/{id}", name="contact_create", methods={"GET","POST"})
     * @param Property $property Property for the contact
     */
    public function new(Request $request, Property $property, \Swift_Mailer $mailer): Response
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

            // On génère l'e-mail
            $message = (new \Swift_Message('Nouvelle demande de rendez-vous'))
                ->setFrom("notification@sinequanone.fr")
                ->setTo($property->getOwner()->getEmail())
                ->setBody(
                    "Bonjour,<br><br>Vous avez reçu une nouvelle demande de rendez-vous pour votre ".$contact->getProperty()->getType()." à "
                    .$contact->getProperty()->getCity()." pour le ". date("d/m/Y \à H:i", $contact->getDesiredDate()->getTimestamp()) .".<br>
                    Connectez-vous sur votre espace personnel pour y répondre.",
                    'text/html'
                );

            // On envoie l'e-mail
            $mailer->send($message);

            $this->addFlash('success', 'Votre demande de RDV a été envoyé !');
            return $this->redirectToRoute('front_contact_mycontacts');
        }

        return $this->render('front/contact/new.html.twig', [
            'contact'   => $contact,
            'property'  => $property,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @Route("/contact/accept/{id}", name="contact_acceptDate", methods={"GET"})
     * @param Contact $contact Contact to accept
     */
    public function acceptDate(Contact $contact, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        if ($contact->getStatus() !== 'RDV_CREE')
        {
            throw $this->createAccessDeniedException('You can\'t do this action !');
        }

        $contact->setStatus('RDV_VALIDE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

         // On génère l'e-mail
         $message = (new \Swift_Message('Rendez-vous accepté'))
         ->setFrom("notification@sinequanone.fr")
         ->setTo($contact->getProspect()->getEmail())
         ->setBody(
             "Bonjour,<br><br>Votre demande de rendez-vous concernant ".$contact->getProperty()->getType()." à ".$contact->getProperty()->getCity()." a été acceptée.<br>
             Connectez-vous sur votre espace personnel pour accéder aux informations.",
             'text/html'
         );

        // On envoie l'e-mail
        $mailer->send($message);

        $this->addFlash('success', 'Date de RDV accepté !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/another/{id}", name="contact_anotherDate", methods={"GET"})
     * @param Contact $contact Contact to ask another date
     */
    public function anotherDate(Contact $contact, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        if ($contact->getStatus() !== 'RDV_CREE')
        {
            throw $this->createAccessDeniedException('You can\'t do this action !');
        }

        $contact->setStatus('RDV_NOUVELLE_DATE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        // On génère l'e-mail
        $message = (new \Swift_Message('Rendez-vous en attente d\'une nouvelle date'))
        ->setFrom("notification@sinequanone.fr")
        ->setTo($contact->getProspect()->getEmail())
        ->setBody(
            "Bonjour,<br><br>Votre demande de rendez-vous concernant ".$contact->getProperty()->getType()." à ".$contact->getProperty()->getCity()." est en attente d'une nouvelle proposition de date.<br>
            Connectez-vous sur votre espace personnel afin de proposer une nouvelle au propriétaire.",
            'text/html'
        );

       // On envoie l'e-mail
       $mailer->send($message);

        $this->addFlash('success', 'Demande de nouvelle date de RDV envoyée !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/decline/{id}", name="contact_decline", methods={"GET"})
     * @param Contact $contact Contact to decline
     */
    public function decline(Contact $contact, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        if ($contact->getStatus() !== 'RDV_CREE' && $contact->getStatus() !== 'RDV_NOUVELLE_DATE')
        {
            throw $this->createAccessDeniedException('You can\'t do this action !');
        }

        $contact->setStatus('RDV_FERME');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        // On génère l'e-mail
        $message = (new \Swift_Message('Rendez-vous refusé'))
        ->setFrom("notification@sinequanone.fr")
        ->setTo($contact->getProspect()->getEmail())
        ->setBody(
            "Bonjour,<br><br>Votre demande de rendez-vous concernant ".$contact->getProperty()->getType()." à ".$contact->getProperty()->getCity()." a été refusée par le propriétaire.<br>
            Connectez-vous sur votre espace personnel afin de proposer une nouvelle au propriétaire.",
            'text/html'
        );

       // On envoie l'e-mail
       $mailer->send($message);

        $this->addFlash('success', 'RDV fermé !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/finish/{id}", name="contact_finish", methods={"GET"})
     * @param Contact $contact Contact to finish
     */
    public function finish(Contact $contact, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::OWNER_CONTACT, $contact);

        if ($contact->getStatus() !== 'RDV_VALIDE')
        {
            throw $this->createAccessDeniedException('You can\'t do this action !');
        }

        $contact->setStatus('RDV_TERMINE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        // On génère l'e-mail
        $message = (new \Swift_Message('Rendez-vous terminé'))
        ->setFrom("notification@sinequanone.fr")
        ->setTo($contact->getProspect()->getEmail())
        ->setBody(
            "Bonjour,<br><br>Votre demande de rendez-vous concernant ".$contact->getProperty()->getType()." à ".$contact->getProperty()->getCity()." est marquée comme terminée.<br>
            Merci d'avoir utilisé Sinequanone !",
            'text/html'
        );

       // On envoie l'e-mail
       $mailer->send($message);

        $this->addFlash('success', 'RDV marqué comme terminé !');
        return $this->redirectToRoute('front_contact_index');
    }

    /**
     * @Route("/contact/new_date/{id}", name="contact_new_date", methods={"POST"})
     * @param Contact $contact Contact to choose new date
     */
    public function newDate(Contact $contact, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted(ContactVoter::PROSPECT_CONTACT, $contact);

        if ($contact->getStatus() !== 'RDV_NOUVELLE_DATE')
        {
            throw $this->createAccessDeniedException('You can\'t do this action !');
        }

        $contact->setStatus('RDV_CREE');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        // On génère l'e-mail
        $message = (new \Swift_Message('Nouvelle date de rendez-vous'))
        ->setFrom("notification@sinequanone.fr")
        ->setTo($contact->getProperty()->getOwner()->getEmail())
        ->setBody(
            "Bonjour,<br><br>Vous avez reçu une nouvelle proposition de date de rendez-vous pour votre ".$contact->getProperty()->getType()." à "
                    .$contact->getProperty()->getCity()." pour le ". date("d/m/Y \à H:i", $contact->getDesiredDate()->getTimestamp()) .".<br>
                    Connectez-vous sur votre espace personnel pour y répondre.",
            'text/html'
        );

       // On envoie l'e-mail
       $mailer->send($message);

        $this->addFlash('success', 'Nouvelle date de RDV envoyée !');
        return $this->redirectToRoute('front_contact_index');
    }

}
