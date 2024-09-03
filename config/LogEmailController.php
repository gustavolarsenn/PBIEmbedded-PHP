<?php

require_once __DIR__ . "/config.php";
require_once CAMINHO_BASE . '/vendor/autoload.php';

require_once CAMINHO_BASE . '/config/ServicoMailer.php';

use Psr\Log\LoggerInterface;


class LogEmailController
{
    private $logger;
    private $mailerService;
    public function __construct(LoggerInterface $logger)
    {
        $this->mailerService = new ServicoMailer();
        $this->logger = $logger;
    }

    public function emailOnLog(string $message, Array $list): void
    {
        $this->mailerService->enviarEmailErro('gustavo.larsen@zport.com.br', 'Erro na aplicação:', $message . '<br>' . implode($list));
    }
}