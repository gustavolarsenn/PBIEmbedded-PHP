<?php
require_once 'Prancha.php';
require_once '../config/database.php';
require_once '../SessionManager.php';

$pdo = (new Database())->getConnection();
$prancha = new Prancha($pdo, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        case 'uniqueVessels':
            $message = $prancha->pegarNaviosUnicos($pdo);
            echo $message;
            break;
        case 'vesselData':
            $message = $prancha->pegarDadosNavio($pdo, $_POST['navio']);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}