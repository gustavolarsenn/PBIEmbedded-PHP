<?php

// namespace App\Controller;
require_once __DIR__ . "\\config.php";
require_once CAMINHO_BASE . '\\vendor\\autoload.php';

require_once CAMINHO_BASE . '\\config\\MailerService.php';

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class LogEmailController
{
    private $logger;
    private $mailerService;

    public function __construct(LoggerInterface $logger, MailerService $mailerService)
    {
        $this->logger = $logger;
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/test-error", name="test_error")
     */
    public function testError(): Response
    {
        $this->logger->error('This is a test error message.');
        $this->mailerService->sendErrorEmail('gustavo.larsen@zport.com.br', 'Error in Application', 'This is a test error message.');

        return new Response('Error logged and email should be sent.');
    }
}