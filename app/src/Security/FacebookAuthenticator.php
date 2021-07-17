<?php

namespace App\Security;

use App\Entity\User;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Security;

class FacebookAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $urlGenerator;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * FacebookAuthenticator constructor.
     * @param ClientRegistry $clientRegistry
     * @param EntityManagerInterface $em
     * @param UserManagerInterface $userManager
     */
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    /**
     * @param Request $request
     * @return \League\OAuth2\Client\Token\AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        return $this->fetchAccessToken($this->getFacebookClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null|object|\Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        $email = $facebookUser->getEmail();


            $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $email]);


            if (!$user) {
                $user = new User();
                $user->setEnabled(1);
                $user->setEmail($facebookUser->getEmail());
                $user->setRoles(["ROLE_USER"]);
                $user->setPassword("");
                $user->setFirstname($facebookUser->getFirstName());
                $user->setLastname($facebookUser->getLastName());
                $user->setCivility("");
                $this->em->persist($user);
                $this->em->flush();

                $this->mailer->sendEmailWelcome($user->getEmail(), $user->getToken(), $user->getFirstname() . ' ' . $user->getLastname());
            }

        return $user;

    }


    /**
     * @return FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry->getClient('facebook');
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        return new RedirectResponse($targetPath ?: '/facebook/inscription');
    }


}
