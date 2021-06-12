<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
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
        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
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
