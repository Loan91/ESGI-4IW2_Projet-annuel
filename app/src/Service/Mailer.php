<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;

class Mailer {
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail($email, $token, $name)
    {
        $email = (new TemplatedEmail())
            ->from('vousloger@noreply.fr')
            ->to(new Address($email))
            ->subject('Confirmation d\'inscription')
            ->htmlTemplate('security/mail/registration.html.twig')
            ->context([
                'token' => $token,
                'name' => $name
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmailWelcome($email, $token, $name)
    {
        $email = (new TemplatedEmail())
            ->from('vousloger@noreply.fr')
            ->to(new Address($email))
            ->subject('Bienvenue chez Easyhouse')
            ->htmlTemplate('security/mail/welcome.html.twig')
            ->context([
                'token' => $token,
                'name' => $name,
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmailContact($email, $token, $name, $message)
    {
        $email = (new TemplatedEmail())
            ->from('vousloger@noreply.fr')
            ->to(new Address($email))
            ->subject('Vous avez reçu un email')
            ->htmlTemplate('security/mail/welcome.html.twig')
            ->context([
                'token' => $token,
                'name' => $name,
                'message' => $message,
            ])
        ;

        $this->mailer->send($email);
    }
}
