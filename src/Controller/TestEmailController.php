<?php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestEmailController
{
    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('recipient@example.com')
            ->subject('Test Email')
            ->text('This is a test email sent from Symfony using MailHog.');

        $mailer->send($email);

        return new Response('Email sent successfully!');
    }
}

