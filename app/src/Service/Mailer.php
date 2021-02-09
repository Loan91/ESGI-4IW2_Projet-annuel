<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer {
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
            ->subject('Confirmation d\'inscription')
            ->htmlTemplate('security/mail/registration.html.twig')
            ->context([
                'token' => $token,
                'name' => $name
            ])
        ;

        $this->mailer->send($email);
    }
}
