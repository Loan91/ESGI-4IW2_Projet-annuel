<?php

namespace App\Controller\Security;


use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/users/pass/modifier", name="app_user_pass_modifier")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        if($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            //verification si les deux mot de passe sont identiques
            if($request->request->get('password') == $request->request->get('password2')){

            }else{
                $this->addFlash('error', 'Les deux mots de passe ne sont pas identique');
            }
        }
        return $this->render('users/editpass.html.twig');
    }
}
