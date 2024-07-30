<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once '../config/database.php';
require_once '../controllers/PbiReportsController.php';
require_once 'PowerBISession.php';
require_once '../models/Capacidade.php';

require_once '../SessionManager.php';

require_once '../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function gerarRelatorioPBI($actualLink){
    define('LOG_FILE', 'PowerBI');
    define('LOG', 'relatorio_pbi');
    define('CAMINHO_LOG', __DIR__ . '/../logs/' . LOG_FILE . '.log');
    
    $capacidade = new Capacidade();

    $log = new Logger(LOG);
    $log->pushHandler(new StreamHandler(CAMINHO_LOG, Logger::DEBUG));

    SessionManager::checarSessao();

    $log->info('Gerando relatório PowerBI', ['user' => $_SESSION['id_usuario']]);
    try {
        $capacidade->criarTarefaChecarCapacidade();
        $conn = (new Database())->getConnection();
        $pbiReports = new PbiReports($conn);
        
        $reports = $pbiReports->getActiveReports();
        
        if (!isset($reports[$actualLink])) {
            $log->error('Relatório não encontrado', ['user' => $_SESSION['id_usuario'], ['report' => $actualLink]]);            
            header('Location: /index.php');
            exit;
        } 

        $currentReport = $reports[$actualLink];
        
        $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
        $powerBISession->criarSessaoPBI();
        
        $azureAPI = new AzureAPI();

        $capacidadeAtiva = $capacidade->ligarCapacity($powerBISession->sessoesAtivasPBI(), $azureAPI);

        if (!json_decode($capacidadeAtiva)->sucesso){
            $log->error('Não foi possível gerar relatório, capacidade não iniciada', ['user' => $_SESSION['id_usuario'], 'report' => $actualLink]);
            return $capacidadeAtiva;
        }

        $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);
        $conn = null;

        $log->info('Relatório gerado com sucesso', ['user' => $_SESSION['id_usuario'], 'report' => $actualLink]);
        return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);

    } catch (Exception $e) {
        $log->error('Erro ao gerar relatório PowerBI: ' . $e->getMessage(), ['user' => $_SESSION['id_usuario'], 'report' => $actualLink]);
    }
}

if($_POST['action'] === 'gerarRelatorioPBI'){
    $actualLink = $_POST['reportName'];
    echo gerarRelatorioPBI($actualLink);
}