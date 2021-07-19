<?php

namespace App\Controller\Front;

use App\Entity\ProfilePicture;
use App\Entity\User;
use App\Form\EditPassType;
use App\Form\UpdateProfileGFType;
use App\Form\EditProfileType;
use App\Security\Voter\ProfilVoter;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route("/profil", name="profil_")
 */
class ProfilUserController extends AbstractController
{

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index()
    {
        $this->denyAccessUnlessGranted(ProfilVoter::VIEW, $this->getUser());
        return $this->render('front/users/index.html.twig', [
            $this->getUser()
        ]);
    }

    /**
     * @Route("/edit", name="edit", methods={"GET", "PATCH"})
     * @param Request $request
     */
    public function editProfil(Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(ProfilVoter::UPDATE, $user);

        $userForm = $this->createForm(EditProfileType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('front_profil_index');
        }

        return $this->render('front/users/editprofile.html.twig', [
            'form' => $userForm->createView(),
        ]);
    }

    /**
     * @Route("/edit-password", name="edit_password", methods={"GET", "PATCH"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted(ProfilVoter::UPDATE, $this->getUser());

        $userForm = $this->createForm(EditPassType::class);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $password = $request->request->get('edit_pass')['password']['first'];
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
            $em->flush();

            $this->addFlash('success', 'Le mot de passe a été mis à jour avec succès');
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('front/users/editpass.html.twig', [
            'form' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/delete", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     */
    public function delete(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(ProfilVoter::DELETE, $user);

        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('The csrf token is invalid');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        $tokenStorage->setToken(null);
        $session->invalidate();

        return $this->redirectToRoute('front_home');
    }
}
