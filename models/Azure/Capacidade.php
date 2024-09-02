<?php

require_once __DIR__ . '\\..\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';    
require_once CAMINHO_BASE . '\\models\\Azure\\AzureAPI.php';
class Capacidade {
    private const LOG_FILE = 'Capacidade';
    private const TENTATIVA_MAX = 10;
    private const TEMPO_ESPERA = 7;

    function ligarCapacity($possuiSessoesAtivasPBI, $azureAPI){
        /* Gerencia capacidade (cluster), e liga quando tiver usuário acessando */
        $log = AppLogger::getInstance(self::LOG_FILE);
        
        $log->info('Iniciando processo para LIGAR capacidade',  ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
    
        if ($possuiSessoesAtivasPBI){
            try {
                for ($i = 0; $i < self::TENTATIVA_MAX; $i++) {
                    $statusCapacity = $azureAPI->pegarStatusCapacity();
                    if ($statusCapacity != 'Pausing' && $statusCapacity != 'Resuming') {
                        if ($statusCapacity == 'Succeeded'){
                            break;
                        }
                        if ($statusCapacity == 'Paused'){
                            $azureAPI->ligarDesligarCapacity(true);
                        }
                    }
                    sleep(self::TEMPO_ESPERA);
    
                }
                if ($statusCapacity != 'Succeeded'){
                    $log->error('10 tentativas de ligar sem sucesso! Verificar o que está impedindo!', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
                    
                    return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível iniciar capacidade para gerar o relatório! Reinicie a página e tente novamente.']);
                }
    
                $log->info('Capacidade iniciada com sucesso!', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
        
                return json_encode(['sucesso' => true, 'mensagem' => 'Capacidade iniciada com sucesso!']);
    
            } catch (Exception $e) {
                $log->error('Erro ao iniciar capacidade', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
    
                return json_encode(['sucesso' => false, 'mensagem' => 'Erro ao iniciar capacidade: ' . $e->getMessage()]);
            }
        } 
    }
}