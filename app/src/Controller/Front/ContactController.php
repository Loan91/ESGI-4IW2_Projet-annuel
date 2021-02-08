<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/compte/rendez-vous", name="contact_index")
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front/contact/index.html.twig', [
            'contacts' => $contactRepository->getContactsOrdered($user->getId()),
        ]);
    }
}
