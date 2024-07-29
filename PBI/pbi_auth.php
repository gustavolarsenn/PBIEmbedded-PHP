<?php
require_once 'AzureAPI.php';
require_once 'PowerBiReportDetails.php';
require_once 'EmbedConfig.php';
require_once 'ApiCalls.php';
require_once '../config/database.php';
require_once '../controllers/PbiReportsController.php';
require_once 'PowerBISession.php';
require_once 'capacidade/CriarTarefaCapacidade.php';

require_once '../SessionManager.php';

require_once '../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function ligarCapacity($possuiSessoesAtivasPBI, $azureAPI){
    /* Gerencia capacidade (cluster), e liga quando tiver usuário acessando */

    // Constants for readability
    define('MAX_RETRIES', 10);
    define('SLEEP_DURATION', 2.5);
    $log = new Logger('gerenciamento_capacidade');
    $log->pushHandler(new StreamHandler(__DIR__ . '/capacidade/gerenciamento_capacidade.log', Logger::DEBUG));

    $log->info('Iniciando processo para LIGAR capacidade',  ['user' => $_SESSION['id_usuario']]);

    if ($possuiSessoesAtivasPBI){
        try {
            for ($i = 0; $i < MAX_RETRIES; $i++) {
                $statusCapacity = $azureAPI->pegarStatusCapacity();
                if ($statusCapacity != 'Pausing' && $statusCapacity != 'Resuming') {
                    if ($statusCapacity == 'Succeeded'){
                        break;
                    }
                    if ($statusCapacity == 'Paused'){
                        $azureAPI->ligarDesligarCapacity(true);
                    }
                }
                sleep(SLEEP_DURATION);

            }
            if ($statusCapacity != 'Succeeded'){
                $log->error('10 tentativas de ligar sem sucesso! Verificar o que está impedindo!', ['user' => $_SESSION['id_usuario']]);
                
                return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível iniciar capacidade para gerar o relatório! Reinicie a página e tente novamente.']);
            }

            $log->info('Capacidade iniciada com sucesso!', ['user' => $_SESSION['id_usuario']]);
    
            return json_encode(['sucesso' => true, 'mensagem' => 'Capacidade iniciada com sucesso!']);

        } catch (Exception $e) {
            $log->error('Erro ao iniciar capacidade: ' . $e->getMessage(),  ['user' => $_SESSION['id_usuario']]);

            return json_encode(['sucesso' => false, 'mensagem' => 'Erro ao iniciar capacidade: ' . $e->getMessage()]);
        }
    } 
}

function gerarRelatorioPBI($actualLink){
    SessionManager::checarSessao();

    try {
        criarTarefaChecarCapacidade();
        $conn = (new Database())->getConnection();
        $pbiReports = new PbiReports($conn);
        
        $reports = $pbiReports->getActiveReports();
        
        if (!isset($reports[$actualLink])) {
            header('Location: /index.php');
            exit;
        } 

        $currentReport = $reports[$actualLink];
        
        $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
        $powerBISession->criarSessaoPBI();
        
        $azureAPI = new AzureAPI();

        $capacidadeAtiva = ligarCapacity($powerBISession->sessoesAtivasPBI(), $azureAPI);

        if (!json_decode($capacidadeAtiva)->sucesso){
            // return json_encode($capacidadeAtiva);
            return $capacidadeAtiva;
        }

        $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['report_id'], $currentReport['dataset_id'], null);
        $conn = null;

        return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

if($_POST['action'] === 'gerarRelatorioPBI'){
    $actualLink = $_POST['reportName'];
    echo gerarRelatorioPBI($actualLink);
}