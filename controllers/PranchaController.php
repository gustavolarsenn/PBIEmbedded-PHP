<?php
require_once __DIR__ . '/../config/config.php';

require_once CAMINHO_BASE . '/models/Prancha.php';
require_once CAMINHO_BASE . '/config/database.php';
require_once CAMINHO_BASE . '/models/SessionManager.php';

$pdo = (new Database())->getConnection();
$prancha = new Prancha($pdo, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        // case 'pegarNaviosUnicos':
        //     $message = $prancha->pegarNaviosUnicos($pdo);
        //     echo $message;
        //     break;
        case 'pegarDadosNavio':
            $message = $prancha->pegarDadosNavio($pdo, $_POST['id_viagem']);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}