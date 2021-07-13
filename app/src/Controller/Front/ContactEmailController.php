<?php

namespace App\Controller\Front;

use App\Form\ContactEmailType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactEmailController extends AbstractController
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactEmailType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('vousloger@noreply.fr')
                ->subject('vous avez reçu unn email')
                ->text($contactFormData['message'],
                    'text/plain');
            $mailer->send($message);

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('front_contact');
        }
        return $this->render('front/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
