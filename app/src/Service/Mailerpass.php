<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailerpass {
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($email, $token, $name)
    {
        $email = (new TemplatedEmail())
            ->from('vousloger@noreply.fr')
            ->to(new Address($email))
            ->subject('Reset mot de passe')
            ->htmlTemplate('security/forgot_password/forgot_password_email.html.twig')
            ->context([
                'token' => $token,
                'name' => $name
            ])
        ;

        $this->mailer->send($email);
    }
}
