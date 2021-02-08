<?php

namespace App\Controller\Front;


use App\Entity\User;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileUserController extends AbstractController
{

    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        return $this->render('front/users/index.html.twig');
    }

    /**
     * @Route("/users/profile/modifier", name="user_profil_modifier")
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
            return $this->redirectToRoute('front_users');
        }

        return $this->render('front/users/editprofile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/pass/modifier", name="user_pass_modifier")
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

                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
                $em->flush();

                $this->addFlash('success', 'Mot de passe mis à jour avec succès');
                return $this->redirectToRoute('front_users');

            }else{
                $this->addFlash('error', 'Les deux mots de passe ne sont pas identique');
            }
        }
        return $this->render('front/users/editpass.html.twig');
    }

    /**
     * @Route("/users/profile/delete/{id}", name="user_pass_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_users');
    }
}
