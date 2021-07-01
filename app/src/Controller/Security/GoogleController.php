<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UpdateProfileGFType;
use App\Form\UpdateProfileGoogleType;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GoogleController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/google", name="connect_google_start")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {

        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile', 'email' // the scopes you want to access
            ])
            ;
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/google/check", name="connect_google_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        if (!$this->getUser()) {
            return new JsonResponse(array('status' => false, 'message' => "Utilisateur non trouver !"));
        } else {
            return $this->redirectToRoute('front_home');
        }
    }

    /**
     * @Route("/google/inscription", name="app_inscriptiongoogle")
     * @param Request $request
     */
    public function updateProfile(Request $request,  UserPasswordEncoderInterface $passwordEncoder)
    {


        $user = $this->getUser();

        $existingUser = $user->getGoogleId();
        if ($existingUser) {
            return $this->redirectToRoute('front_profil_index');
        } else {


            $userForm = $this->createForm(UpdateProfileGFType::class, $user);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setGoogleId(md5(random_bytes(10)));
                $password = $userForm->get('password')->getData();
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Inscription réussi');
                return $this->redirectToRoute('front_profil_index');
            }

            return $this->render('security/google/updateprofilegoogle.html.twig', [
                'form' => $userForm->createView(),
            ]);
        }
    }
}
