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
        // case 'readUnique':
        //     $message = $shipDischarging->pegarUnicos($pdo, $_POST['campo'], $_POST['where']);
        //     echo $message;
        //     break;
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
        // case 'totalDescarregado':
        //     $message = $shipDischarging->totalDescarregado($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        // case 'totalPlanejado':
        //     $message = $shipDischarging->totalPlanejado($pdo, $_POST['where'], 'planejado');
        //     echo $message;
        //     break;
        // case 'descarregadoClienteArmazemDI':
        //     $message = $shipDischarging->descarregadoClienteArmazemDI($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        // case 'descarregadoPorao':
        //     $message = $shipDischarging->descarregadoPorao($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        // case 'descarregadoDia':
        //     $message = $shipDischarging->descarregadoDia($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        // case 'descarregadoCliente':
        //     $message = $shipDischarging->descarregadoCliente($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        // case 'descarregadoDiaPeriodo':
        //     $message = $shipDischarging->descarregadoDiaPeriodo($pdo, $_POST['where'],);
        //     echo $message;
        //     break;
        default:
            echo json_encode(['message' => 'Ação não encontrada']);
            break;
    }
    // if ($action === 'readAll') {

    //     $message = ShipDischarging::readAll($pdo);
    //     echo $message;
    // } else if ($action === 'readQuery') {
    //     $shipDischarging = new ShipDischarging($pdo, null, null, null, null, null, null, null, null, null, null, null, null);
    //     $message = $shipDischarging->readQuery($pdo, $_POST['select'], $_POST['group_by'], $_POST['order_by'], $_POST['limit'], $_POST['where'], $_POST['column_agg'], $_POST['type_agg']);
    //     echo $message;
    // } else if ($action === 'readUnique') {
    // $shipDischarging = new ShipDischarging($pdo, null, null, null, null, null, null, null, null, null, null, null, null);
    // $message = $shipDischarging->pegarUnicos($pdo, $_POST['campo']);
    // echo $message;
    // }
    
}