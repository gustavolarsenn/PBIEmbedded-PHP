<?php
require_once 'shipDischarging.php';
require_once '../config/database.php';
require_once '../SessionManager.php';

$pdo = (new Database())->getConnection();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'readAll') {

        $message = ShipDischarging::readAll($pdo);
        echo $message;
    } else if ($action === 'readQuery') {
        $shipDischarging = new ShipDischarging($pdo, null, null, null, null, null, null, null, null, null, null, null, null);
        $message = $shipDischarging->readQuery($pdo, $_POST['select'], $_POST['group_by'], $_POST['order_by'], $_POST['limit'], $_POST['where'], $_POST['column_agg'], $_POST['type_agg']);
        echo $message;
    }
    
}