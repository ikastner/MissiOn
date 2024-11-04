<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService {
    private MailerInterface $mailer;

    public function __construct( MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail( string $to, string $subjet, string $content ): void
    {
        //Création et envoi de l'email
        $email = (new Email())
        ->from('ousmane.nndome@gmail')
        ->to($to)
        ->subject($subjet)
        ->text($content);
        $this->mailer->send($email);
    }

}