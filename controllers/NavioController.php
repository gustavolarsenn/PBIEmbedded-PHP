<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\models\\Navio.php';
require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\SessionManager.php';

$pdo = (new Database())->getConnection();
$prancha = new Navio($pdo, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        case 'vesselInfo':
            $message = $prancha->pegarInfoNavio($pdo, $_POST['navio']);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}