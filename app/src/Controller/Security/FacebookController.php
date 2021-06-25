<?php

namespace App\Controller\Security;

use App\Service\Mailer;
use App\Form\UpdateProfileGFType;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FacebookController extends AbstractController
{

    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/facebook", name="connect_facebook_start")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/connect/facebook/check", name="connect_facebook_check")
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
     * @Route("/facebook/inscription", name="app_inscriptionfacebook")
     * @param Request $request
     */
    public function updateProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $existingUser = $user->getEmail();

        if ($existingUser) {
            return $this->redirectToRoute('front_profil_index');
        } else {

            $userForm = $this->createForm(UpdateProfileGFType::class, $user);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setFacebookId(md5(random_bytes(10)));
                $password = $userForm->get('password')->getData();
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Inscription réussi');
                return $this->redirectToRoute('front_profil_index');
            }

            return $this->render('security/facebook/updateprofilefacebook.html.twig', [
                'form' => $userForm->createView(),
            ]);
        }
    }
}
