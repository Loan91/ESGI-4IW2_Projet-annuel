<?php

namespace App\Controller\Security;


use App\Form\EditProfileType;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileUserController extends AbstractController
{

    /**
     * @Route("/users", name="app_users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/profile/modifier", name="app_user_profil_modifier")
     * @param Request $request
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('app_users');
        }

        return $this->render('users/editprofile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
