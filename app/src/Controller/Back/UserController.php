<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\Back\ManageUserType;
use App\Repository\UserRepository;
use App\Security\Voter\ManageUserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Allow to manage users
 * 
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted(ManageUserVoter::VIEW, User::class);

        return $this->render('back/user/index.html.twig', [
            'paginator' => $userRepository->getUsersPaginated($request, 6)
        ]);
    }

    /**
     * @Route("/{user}/delete", name="delete", methods={"DELETE"})
     */
    public function delete(User $user, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted(ManageUserVoter::DELETE, $user);

        // Check the csrf token
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-user', $submittedToken)) {
            throw new InvalidCsrfTokenException("Le token d'action est invalide");
        }

        // Remove the user
        $em->remove($user);
        $em->flush();

        // Redirect with success message
        $this->addFlash('success', "L'utilisateur " . $user->getEmail() . " a bien été supprimé");
        return $this->redirect($previousPage = $request->headers->get('referer'));
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted(ManageUserVoter::CREATE, User::class);

        $userForm = $this->createForm(ManageUserType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été créé');
            return $this->redirectToRoute('back_user_index');
        }

        return $this->render('back/user/create.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/edit/{user}", name="edit", methods={"GET", "PATCH"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted(ManageUserVoter::UPDATE, $user);

        $userForm = $this->createForm(ManageUserType::class, $user, ['method' => 'PATCH']);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur ' . $user->getEmail() . ' a bien été mis à jour');
            return $this->redirectToRoute('back_user_index');
        }

        return $this->render('back/user/edit.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

        /**
     * @Route("/{user}/status/toggle", name="status_toggle", methods={"PATCH"})
     */
    public function toggleStatus(User $user, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted(ManageUserVoter::TOGGLE_ENABLED, $user);

        // Check the csrf token
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('toggle-user-status', $submittedToken)) {
            throw new InvalidCsrfTokenException("Le token d'action est invalide");
        }

        // Toggle status
        if ($user->isEnabled()) {
            $user->disable();
        } else {
            $user->enable();
        }
        $em->flush();

        // Redirect with success message
        $this->addFlash('success', "L'utilisateur " . $user->getEmail() . " a bien été " . ($user->isEnabled() ? 'activé' : 'désactivé'));
        return $this->redirect($previousPage = $request->headers->get('referer'));
    }

}
