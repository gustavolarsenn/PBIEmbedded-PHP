<?php

define('CAMINHO_BASE', __DIR__ . '\\..\\');

// Set error reporting level
error_reporting(E_ALL);

// Set the log file for errors
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '\\..\\logs\\project_errors.log');

require_once CAMINHO_BASE . '\\vendor\\autoload.php';
require_once CAMINHO_BASE . '\\config\\ServicoMailer.php';
require_once CAMINHO_BASE . '\\config\\EmailErrorHandler.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// Configure the logger
$logger = new Logger('my_logger');
$logger->pushHandler(new StreamHandler(CAMINHO_BASE . '\\logs\\project_errors.log', Logger::DEBUG));

// Create the MailerService
$mailerService = new ServicoMailer();

// Create and register the custom email error handler for ERROR level and above
$emailErrorHandler = new EmailErrorHandler(null, null, Logger::ERROR);
$logger->pushHandler($emailErrorHandler);

// Register a shutdown function to handle fatal errors
register_shutdown_function(function () use ($logger) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $logger->critical('Fatal error: ' . $error['message'], ['file' => $error['file'], 'line' => $error['line']]);
    }
});