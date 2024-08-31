<?php

require_once __DIR__ . "\\config.php";

require_once CAMINHO_BASE . '\\config\\ServicoMailer.php';
require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class EmailErrorHandler extends AbstractProcessingHandler
{
    private $mailerService;
    private $recipient;

    public function __construct(ServicoMailer $mailerService = null, string $recipient = null, $level = Logger::ERROR, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->mailerService = $mailerService ?? new ServicoMailer();
        $this->recipient = $recipient ?? getenv('MAILER_RECIPIENT');
    }

    protected function write(LogRecord $record): void
    {
        $message = $record['message'];
        $context = $record['context'];
        $subject = 'Erro na aplicação: ' . $record['level_name'];
        $body = $message . '<br>' . implode('<br>', $context);

        $this->mailerService->enviarEmailErro($this->recipient, $subject, $body);
    }
}