<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\models\\DescarregamentoNavio.php';
require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\SessionManager.php';

$pdo = (new Database())->getConnection();
$descarregamentoNavio = new DescarregamentoNavio($pdo, null, null, null, null, null, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        case 'buscarTodosDados':
            $message = DescarregamentoNavio::buscarTodosDados($pdo);
            echo $message;
            break;
        case 'pegarNaviosUnicos':
            $message = $descarregamentoNavio->pegarNaviosUnicos($pdo);
            echo $message;
            break;
        case 'pegarDadosNavioRealizado':
            $message = $descarregamentoNavio->pegarDadosNavioRealizado($pdo, $_POST['navio']);
            echo $message;
            break;
        case 'pegarDadosNavioPlanejado':
            $message = $descarregamentoNavio->pegarDadosNavioPlanejado($pdo, $_POST['navio']);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}