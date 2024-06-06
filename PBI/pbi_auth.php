<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once 'config/database.php';
require_once 'controller/pbi_reports.php';
require_once 'PowerBISession.php';

function pbi($actualLink){
    
    $conn = (new Database())->getConnection();
    $pbiReports = new PbiReports($conn);
    
    $reports = $pbiReports->getActiveReports();
    
    $currentReport = null;
    if (isset($reports[$actualLink])) {
        $currentReport = $reports[$actualLink];
    } 
    else {
        header('Location: /index.php');
        exit;
    }
    
    $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
    $powerBISession->criarSessaoPBI();
    
    $azureAPI = new AzureAPI();

    if ($powerBISession->sessoesAtivasPBI()){
        while (true){
            $statusCapacity = $azureAPI->pegarStatusCapacity();
            if ($statusCapacity == 'Succeeded'){
                break;
            }
            if ($statusCapacity == 'Paused' || $statusCapacity == 'Pausing'){
                $azureAPI->gerenciarCapacity(true);
            }
            sleep(1);
        }
    } 

    $conn = null;

    $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);

    return json_encode($reportEmbedConfig);
}