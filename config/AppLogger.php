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

    private function __construct($nome_log, $caminho_log = CAMINHO_BASE . '\\logs\\', $emailErrorHandler = null)
    {
        $this->caminho_log = $caminho_log;
        $this->logger = new Logger('app');
        $this->emailErrorHandler = $emailErrorHandler ?? new EmailErrorHandler();
        $this->nome_log = $nome_log;
        $this->iniciarLogger();
    }

    public static function getInstance($nome_log, $caminho_log = CAMINHO_BASE . '\\logs\\', $emailErrorHandler = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($nome_log, $caminho_log, $emailErrorHandler);
        }
        return self::$instance->logger;
    }

    private function iniciarLogger()
    {
        // $this->verificarDiretorioLog();
        $this->logger->pushHandler(new StreamHandler($this->caminho_log . date('Y') . '\\' . date('m') . '\\' . $this->nome_log, Logger::DEBUG));
        $this->logger->pushHandler($this->emailErrorHandler);
    }

    private function verificarDiretorioLog()
    /* Parece não ser necessário no momento, já que o Monologger ou o próprio PHP criam o diretório automaticamente */
    {
        $logDir = $this->caminho_log . date('Y') . '\\' . date('m');
        if (!file_exists($logDir)) {
            if (!mkdir($logDir, 0777, true) && !is_dir($logDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $logDir));
            }
        }
    }
}