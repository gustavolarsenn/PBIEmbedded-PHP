<?php

require_once __DIR__ . '/config.php';
require_once CAMINHO_BASE . '/utils/config/ServicoMailer.php';
require_once CAMINHO_BASE . '/vendor/autoload.php';

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class EmailErrorHandler extends AbstractProcessingHandler
{
    private $mailerService;
    private $recipient;
    private static $storageFile = __DIR__ . '/last_email_sent_times.json';
    private static $lastEmailSentTime = [];

    public function __construct(ServicoMailer $mailerService = null, string $recipient = null, $level = Logger::ERROR, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->mailerService = $mailerService ?? new ServicoMailer();
        $this->recipient = $recipient ?? getenv('MAILER_RECIPIENT');
        $this->loadLastEmailSentTimes();
    }

    protected function write(LogRecord $record): void
    {
        $user = $record->context['user'] ?? 'unknown';
        $errorHash = md5($record->message . $user);
        $currentTime = time();

        if (isset(self::$lastEmailSentTime[$errorHash]) && ($currentTime - self::$lastEmailSentTime[$errorHash] < 60)) {
            return;
        }

        // Send the email
        $message = $record->message;
        $context = $record->context;
        $subject = 'Erro na aplicação ' . $record['level_name'];
        $body = $message . '<br>' . implode('<br>', $context);

        # TODO: Uncomment this line to send the email
        // $this->mailerService->enviarEmailErro($this->recipient, $subject, $body);

        // Update the last email sent time
        self::$lastEmailSentTime[$errorHash] = $currentTime;
        $this->saveLastEmailSentTimes();
    }

    private function loadLastEmailSentTimes(): void
    {
        if (file_exists(self::$storageFile)) {
            $data = file_get_contents(self::$storageFile);
            self::$lastEmailSentTime = json_decode($data, true) ?? [];
        }
    }

    private function saveLastEmailSentTimes(): void
    {
        file_put_contents(self::$storageFile, json_encode(self::$lastEmailSentTime));
    }
}