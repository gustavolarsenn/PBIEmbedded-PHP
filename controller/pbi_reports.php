<?php

require_once $_SERVER['DOCUMENT_ROOT'].'../config/database.php';

class PbiReports{
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getActiveReports(){
        $sql = "SELECT * FROM pbi_reports WHERE is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $reportsArray = array_reduce($reports, function($carry, $report) {
        $carry[$report['report_name']] = [
            "report_id" => $report['report_id'],
            "dataset_id" => $report['dataset_id'],
            "rls" => $report['rls']
        ];
            return $carry;
        }, []);

        return $reportsArray;
    }

}
$pdo = (new Database())->getConnection();
$pbiReports = new PbiReports($pdo);

// Verifica se a ação 'getActiveReports' foi solicitada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'getActiveReports') {
    $reports = $pbiReports->getActiveReports();

    // Envia o retorno da função como uma resposta JSON
    header('Content-Type: application/json');
    echo json_encode($reports);
}