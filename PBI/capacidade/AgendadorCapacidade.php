<?php

// $diretorioAtual = getcwd();
$diretorioAtual = __DIR__;

require_once $diretorioAtual . '\\..\\AzureAPI.php';
require_once $diretorioAtual .'\\..\\PowerBiReportDetails.php';
require_once $diretorioAtual .'\\..\\EmbedConfig.php';
require_once $diretorioAtual .'\\..\\ApiCalls.php';
require_once $diretorioAtual .'\\..\\..\\config\\database.php';

require_once $diretorioAtual .'\\..\\PowerBISession.php';

require_once $diretorioAtual .'\\..\\..\\SessionManager.php';

require_once $diretorioAtual .'\\..\\..\\vendor\\autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function desligarCapacity(){
    /* Gerencia capacidade (cluster), e desliga quando NÃO tiver usuário acessando */
    try {
        define('MAX_RETRIES', 10);
        define('SLEEP_DURATION', 2.5);
        $conn = (new Database())->getConnection();

        $powerBISession = new PowerBISession($conn, null);
        $possuiSessoesAtivasPBI = $powerBISession->sessoesAtivasPBI();

        $azureAPI = new AzureAPI();
        $log = new Logger('gerenciamento_capacidade');
        $log->pushHandler(new StreamHandler(__DIR__ . '/gerenciamento_capacidade.log', Logger::DEBUG));
        
        if (!$possuiSessoesAtivasPBI){
            $log->info('Iniciando processo de DESLIGAR de capacidade');
                for ($i = 0; $i < MAX_RETRIES; $i++) {
                    $statusCapacity = $azureAPI->pegarStatusCapacity();
                    if ($statusCapacity != 'Pausing' && $statusCapacity != 'Resuming') {
                        if ($statusCapacity == 'Succeeded'){
                            $azureAPI->ligarDesligarCapacity(false);
                        }
                        if ($statusCapacity == 'Paused'){
                            break;
                        }
                    }
                    sleep(SLEEP_DURATION);
                }
                if ($statusCapacity == 'Succeeded'){
                    $log->error('10 tentativas de desligar sem sucesso! Verificar o que está impedindo!');
                    return;
                }
                $log->info('Capacidade DESLIGADA com sucesso!');
                return;
            }
    } catch (Exception $e) {
        $log->error('Erro ao DESLIGAR capacidade: ' . $e->getMessage());
    }
}

desligarCapacity();