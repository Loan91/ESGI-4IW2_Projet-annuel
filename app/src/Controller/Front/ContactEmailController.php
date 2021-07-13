<?php

namespace App\Controller\Front;

use App\Form\ContactEmailType;
use App\Service\Mailer;
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
    public function contact(Request $request)
    {
        $form = $this->createForm(ContactEmailType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $this->mailer->sendEmailContact($user->getEmail(), $user->getToken(), $user->getFirstname() . ' ' . $user->getLastname());

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render('front/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
