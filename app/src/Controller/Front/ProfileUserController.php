<?php

namespace App\Controller\Front;


use App\Entity\User;
use App\Form\EditPassType;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
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

        $form = $this->createForm(EditPassType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $password = $request->request->get('edit_pass')['password']['first'];
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
            $em->flush();

            $this->addFlash('success', 'Le mot de passe a été mis à jour avec succès');
            return $this->redirectToRoute('front_users');
        }

        return $this->render('front/users/editpass.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/profile/delete/{id}", name="user_pass_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     */
    public function delete(Request $request, User $user)
    {
        $session = new Session();


        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $session->invalidate();
        }

        return $this->redirectToRoute('front_default_index');
    }
}
