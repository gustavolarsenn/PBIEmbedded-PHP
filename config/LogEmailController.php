<?php

// namespace App\Controller;
require_once __DIR__ . "\\config.php";
require_once CAMINHO_BASE . '\\vendor\\autoload.php';

require_once CAMINHO_BASE . '\\config\\MailerService.php';

use Psr\Log\LoggerInterface;


class LogEmailController
{
    private $logger;
    private $mailerService;
    public function __construct(LoggerInterface $logger)
    {
        $this->mailerService = new MailerService();
        $this->logger = $logger;
    }

    public function emailOnLog(string $message, Array $list): void
    {
        $this->mailerService->sendErrorEmail('gustavo.larsen@zport.com.br', 'Error in Application', $message . '<br>' . implode($list));
    }
}