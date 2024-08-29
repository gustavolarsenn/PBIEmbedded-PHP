<?php

require_once __DIR__ . "\\config.php";
require_once CAMINHO_BASE . '\\vendor\\autoload.php';

require_once CAMINHO_BASE . '\\config\\LogEmailController.php';
require_once CAMINHO_BASE . '\\config\\MailerService.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

// Configure the logger
$logger = new Logger('my_logger');
$logger->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));

// Create the DSN string
$dsn = sprintf('%s://%s:%s@%s:%s?encryption=%s', getenv('MAILER_DRIVER'), getenv('MAILER_USER'), getenv('MAILER_PASSWORD'), getenv('MAILER_HOST'), getenv('MAILER_PORT'), getenv('MAILER_ENCRYPTION'));

// Print the DSN for debugging purposes
print_r($dsn);

// Create the transport
$transport = Transport::fromDsn($dsn);

// Create the mailer
$mailer = new Mailer($transport);

// Create the MailerService
$mailerService = new MailerService($mailer);

// Create the controller
$controller = new LogEmailController($logger, $mailerService);

// Call the controller method
$response = $controller->testError();
echo $response->getContent();