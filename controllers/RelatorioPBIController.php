<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\models\\PBI\\RelatorioPBI.php';
require_once CAMINHO_BASE . '\\config\\database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $pdo = (new Database())->getConnection();
    $relatoriPBI = new RelatorioPBI($pdo);

    switch ($_POST['action']) {
        case 'gerarRelatorioPBI':
            $actualLink = $_POST['reportName'];
            echo $relatoriPBI->gerarRelatorioPBI($actualLink);
            break;
        case 'pegarRelatoriosAtivos':
            $reports = $relatoriPBI->pegarRelatoriosAtivos();
            header('Content-Type: application/x-www-form-urlencoded');
            echo json_encode($reports);
            break;
        default:
            break;
    }
}