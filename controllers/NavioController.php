<?php

require_once __DIR__ . '/../utils/config/config.php';

require_once CAMINHO_BASE . '/models/Navio.php';
require_once CAMINHO_BASE . '/utils/config/database.php';
require_once CAMINHO_BASE . '/models/SessionManager.php';

$pdo = (new Database())->getConnection();
$navio = new Navio($pdo, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        case 'pegarInfoNavio':
            $message = $navio->pegarInfoNavio($pdo, $_POST['id_viagem']);
            echo $message;
            break;
        case 'pegarNaviosUnicos':
            $message = $navio->pegarNaviosUnicos($pdo);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}