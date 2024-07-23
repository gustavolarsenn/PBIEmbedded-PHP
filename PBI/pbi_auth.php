<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once '../../config/database.php';
require_once '../../controllers/PbiReportsController.php';
require_once 'PowerBISession.php';

function pbi($actualLink){
    
    try {

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
            for ($i = 0; $i < 10; $i++) {
                $statusCapacity = $azureAPI->pegarStatusCapacity();
                if ($statusCapacity == 'Succeeded'){
                    $conn = null;
                    
                    $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);
                    
                    return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);
                }
                if ($statusCapacity == 'Paused' || $statusCapacity == 'Pausing'){
                    $azureAPI->gerenciarCapacity(true);
                }
                // sleep(2.5);
                sleep(1);
            }
            $conn = null;
            return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível iniciar capacidade para gerar o relatório!']);
        } 
    

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}