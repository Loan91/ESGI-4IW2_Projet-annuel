<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
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
    public function index(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $em->createQuery("SELECT u FROM App\Entity\User u ORDER BY u.id");
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        // dd($pagination);

        return $this->render('back/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{user}/status/toggle", name="status_toggle", methods={"PATCH"})
     */
    public function toggleStatus(User $user, EntityManagerInterface $em, Request $request)
    {
        // Check if the user has the rights
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits pour supprimer un utilisateur");
        }

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
        $this->addFlash('success', "L'utilisateur " . $user->getEmail() . " a bien été ". ($user->isEnabled() ? 'activé' : 'désactivé'));
        return $this->redirectToRoute('back_user_index');
    }

    /**
     * @Route("/{user}/delete", name="delete", methods={"DELETE"})
     */
    public function deleteUser(User $user, EntityManagerInterface $em, Request $request)
    {
        // Check if the user has the rights
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits pour supprimer un utilisateur");
        }

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
        return $this->redirectToRoute('back_user_index');
    }
}
