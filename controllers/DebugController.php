<?php 

// src/Controller/DebugController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class DebugController extends AbstractController
{
    public function testLogger(LoggerInterface $logger): Response
    {
        $logger->error('This is a test error message!');
        return new Response('Test error logged!');
    }
}