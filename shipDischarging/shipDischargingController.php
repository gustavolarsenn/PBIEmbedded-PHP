<?php
require_once 'shipDischarging.php';
require_once '../config/database.php';
require_once '../SessionManager.php';

$pdo = (new Database())->getConnection();
$shipDischarging = new ShipDischarging($pdo, null, null, null, null, null, null, null, null, null, null, null, null);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch($action){
        case 'readAll':
            $message = ShipDischarging::readAll($pdo);
            echo $message;
            break;
        case 'uniqueVessels':
            $message = $shipDischarging->pegarNaviosUnicos($pdo);
            echo $message;
            break;
        case 'vesselDataDischarged':
            $message = $shipDischarging->pegarDadosNavioRealizado($pdo, $_POST['navio']);
            echo $message;
            break;
        case 'vesselDataPlanned':
            $message = $shipDischarging->pegarDadosNavioPlanejado($pdo, $_POST['navio']);
            echo $message;
            break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
}