<?php

require_once __DIR__ . '\\config.php';

require_once CAMINHO_BASE . '\\config\\EmailErrorHandler.php';
require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AppLogger
{
    private static $instance = null;
    private $logger;
    private $caminho_log;
    private $emailErrorHandler;
    private $nome_log;
    private static $loggers = [];
    private function __construct($nome_log, $caminho_log = CAMINHO_BASE . '\\logs\\', $emailErrorHandler = null)
    {
        $this->caminho_log = $caminho_log;
        $this->logger = new Logger('app');
        $this->emailErrorHandler = $emailErrorHandler ?? new EmailErrorHandler();
        $this->nome_log = $nome_log;
        // $this->iniciarLogger();
    }

    public static function getInstance($nome_log, $caminho_log = CAMINHO_BASE . '\\logs\\', $emailErrorHandler = null)
    {
        $log_key = $nome_log;
        
        if (!isset(self::$loggers[$log_key])) {
            $emailErrorHandler = $emailErrorHandler ?? new EmailErrorHandler();
            $logger = new Logger($nome_log);
            $logger->pushHandler(new StreamHandler($caminho_log . date('Y') . '\\' . date('m') . '\\' . $nome_log . '.log',Logger::DEBUG));
            $logger->pushHandler($emailErrorHandler);

            self::$loggers[$log_key] = $logger;
        }

        return self::$loggers[$log_key];
    }
}