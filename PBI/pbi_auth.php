<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once '../config/database.php';
require_once '../controllers/PbiReportsController.php';
require_once 'PowerBISession.php';

require_once '../SessionManager.php';

function gerarRelatorioPBI($actualLink){
    SessionManager::checarSessao();

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
    
        // if ($powerBISession->sessoesAtivasPBI()){
        //     for ($i = 0; $i < 10; $i++) {
        //         $statusCapacity = $azureAPI->pegarStatusCapacity();
        //         if ($statusCapacity == 'Succeeded'){
        //             $conn = null;
                    
                    
        //         }
        //         if ($statusCapacity == 'Paused' || $statusCapacity == 'Pausing'){
        //             $azureAPI->gerenciarCapacity(true);
        //         }
        //         // sleep(2.5);
        //         sleep(1);
        //     }
        //     return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível iniciar capacidade para gerar o relatório!']);
        // } 
        $token = $azureAPI->pegarAuthToken();
        
        $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);
        $statusCapacity = $azureAPI->pegarStatusCapacity();
        $conn = null;
        
        if ($statusCapacity == 'Paused' || $statusCapacity == 'Pausing'){
            return json_encode(['sucesso' => false, 'mensagem' => 'Capacidade não está disponível para gerar o relatório!']);
        }

        // $reportInfo = [
        //     'report_id' => $currentReport['report_id'],
        //     'report_name' => $currentReport['report_name'],
        //     'token' => $token,
        // ];

        return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

if($_POST['action'] === 'gerarRelatorioPBI'){
    $actualLink = $_POST['reportName'];
    echo gerarRelatorioPBI($actualLink);
}