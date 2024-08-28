<?php 

// src/Controller/EmailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController
{
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('your-email@example.com')
            ->to('recipient@example.com')
            ->subject('Hello from Symfony Mailer!')
            ->text('This is a test email sent using Symfony Mailer.');

        $mailer->send($email);

        return new Response('Email sent successfully!');
    }
}