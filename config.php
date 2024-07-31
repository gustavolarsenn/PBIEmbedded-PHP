<?php

define('CAMINHO_BASE', __DIR__);

// Set error reporting level
error_reporting(E_ALL);

// Set the log file for errors
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/logs/project_errors.log');
