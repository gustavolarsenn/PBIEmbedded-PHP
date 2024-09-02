<?php

define('CAMINHO_BASE', __DIR__ . '\\..\\');

// Set error reporting level
error_reporting(E_ALL);

// Set the log file for errors
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '\\..\\logs\\project_errors.log');

require_once __DIR__ . '\\AppLogger.php';

// Configure the logger
$logger = AppLogger::getInstance('project_errors.log');

// Register a shutdown function to handle fatal errors
register_shutdown_function(function () use ($logger) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $logger->critical('Fatal error: ' . $error['message'], ['file' => $error['file'], 'line' => $error['line']]);
    }
});