<?php

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
class ServicoMailer
{
    private $mailer;
    private $mailer_driver;
    private $mailer_user;
    private $mailer_password;
    private $mailer_host;
    private $mailer_port;
    private $mailer_encryption;
    private $transport;
    public function __construct()
    {
        $this->mailer_driver = getenv('MAILER_DRIVER');
        $this->mailer_user = getenv('MAILER_USER');
        $this->mailer_password = getenv('MAILER_PASSWORD');
        $this->mailer_host = getenv('MAILER_HOST');
        $this->mailer_port = getenv('MAILER_PORT');
        $this->mailer_encryption = getenv('MAILER_ENCRYPTION');
        $this->transport = Transport::fromDsn(sprintf('%s://%s:%s@%s:%s?encryption=%s', $this->mailer_driver, $this->mailer_user, $this->mailer_password, $this->mailer_host, $this->mailer_port, $this->mailer_encryption));
        $this->mailer = new Mailer($this->transport);
    }

    public function enviarEmailErro(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from($this->mailer_user)
            ->to($to)
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);
    }
}