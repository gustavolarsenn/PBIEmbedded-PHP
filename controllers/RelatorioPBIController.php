<?php

require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\models\\PBI\\RelatorioPBI.php';
require_once CAMINHO_BASE . '\\config\\database.php';

require_once CAMINHO_BASE . '\\config\\AppLogger.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $pdo = (new Database())->getConnection();
    $relatoriPBI = new RelatorioPBI($pdo);
    $logger = AppLogger::getInstance('RelatorioPBIController');

    try {
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
            case 'buscarInformacoesRelatorio':
                $reportName = $_POST['reportName'];
                $report = $relatoriPBI->buscarInformacoesRelatorio($reportName);
                header('Content-Type: application/x-www-form-urlencoded');
                echo json_encode($report);
                break;
            default:
                break;
        }
    } catch (Exception $e) {
        $logger->error($e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
    }
}