<?php

require_once __DIR__ . '\\..\\..\\config.php';

require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';    
require_once CAMINHO_BASE . '\\models\\Azure\\AzureAPI.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Capacidade {
    private const LOG = 'capacidade';
    private const LOG_AGENDADOR = 'agendador_capacidade';
    private const LOG_FILE = 'Capacidade';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
    private const TENTATIVA_MAX = 10;
    private const TEMPO_ESPERA = 3;

    function ligarCapacity($possuiSessoesAtivasPBI, $azureAPI){
        /* Gerencia capacidade (cluster), e liga quando tiver usuário acessando */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
    
        $log->info('Iniciando processo para LIGAR capacidade',  ['user' => $_SESSION['id_usuario']]);
    
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
    
    function desligarCapacity(){
        /* Gerencia capacidade (cluster), e desliga quando NÃO tiver usuário acessando */
        try {
            $conn = (new Database())->getConnection();
            
            $powerBISession = new PowerBISession($conn, null);
            $possuiSessoesAtivasPBI = $powerBISession->sessoesAtivasPBI();
            
            $azureAPI = new AzureAPI();
            $log = new Logger(self::LOG);
            $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
            
            if (!$possuiSessoesAtivasPBI){
                $log->info('Iniciando processo de DESLIGAR de capacidade');
                for ($i = 0; $i < self::TENTATIVA_MAX; $i++) {
                    $statusCapacity = $azureAPI->pegarStatusCapacity(); // Aqui ta dando pau com o ChecarSessão em AzureAPI.php

                    if ($statusCapacity != 'Pausing' && $statusCapacity != 'Resuming') {
                        if ($statusCapacity == 'Succeeded'){
                            $azureAPI->ligarDesligarCapacity(false);
                        }
                        if ($statusCapacity == 'Paused'){
                            break;
                        }
                    }
                    sleep(self::TEMPO_ESPERA);
                }
                if ($statusCapacity == 'Succeeded'){
                    $log->error('10 tentativas de desligar sem sucesso! Verificar o que está impedindo!');
                    return;
                }
                    $log->info('Capacidade DESLIGADA com sucesso!');
                    return;
                }
            $log->info('Capacidade já está DESLIGADA!');
            return;
        } catch (Exception $e) {
            $log->error('Erro ao DESLIGAR capacidade: ' . $e->getMessage());
        }
    }
    
    function criarArquivoBat($nomeArquivo){
        /* Cria arquivo .bat que vai executar script através do Agendador de Tarefas do Windows */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {        
            if (!file_exists($nomeArquivo)) {
                $conteudo = '@echo off' . PHP_EOL;
                $conteudo .= 'start /min php ' . CAMINHO_BASE . '\\config\\capacidade\\AgendadorCapacidade.php' . PHP_EOL;
                $conteudo .= 'exit'; 
    
                file_put_contents(CAMINHO_BASE . '\\config\\capacidade\\' . $nomeArquivo, $conteudo);
    
                $log->info('Arquivo bat criado com sucesso.');
            }
        } catch (Exception $e) {
            $log->error('Erro ao criar arquivo bat: ' . $e->getMessage());
        }
    }

    function criarTarefaChecarCapacidade(){
        /*
        Cria uma tarefa no Windows Task Scheduler para checar a capacity (cluster Azure) a cada 10 minutos.
        */
        try {
            $log = new Logger(self::LOG_AGENDADOR);
            $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
            
            $nomeArquivoBat = 'capacity.bat';
    
            self::criarArquivoBat($nomeArquivoBat);
            
            $caminhoScript = CAMINHO_BASE . '\\config\\capacidade\\' . $nomeArquivoBat;
            $nomeTarefa = 'AzureCapacityScheduler';
            $periodicidade = 'minute';
            $intervalo = 10;
            
            // Verifica se a tarefa existe
            $verificarTarefaExiste = "schtasks /query /tn \"$nomeTarefa\"";
            exec($verificarTarefaExiste, $output, $returnVar);
            
            if($returnVar !== 0){
                $comando = "schtasks /create /tn \"$nomeTarefa\" /tr \"$caminhoScript\" /sc $periodicidade /mo $intervalo /f";
        
                exec($comando, $output, $returnVar);
            
                // Verifica se a tarefa foi criada com sucesso
                if ($returnVar === 0) {
                    $log->info("Tarefa '$nomeTarefa' criada com sucesso.");
                } else {
                    $log->error("Erro ao criar tarefa '$nomeTarefa'. Output: " . implode("\n", $output));
                }
            }
        } catch (Exception $e) {        
            $log->error('Erro ao criar tarefa para checar capacidade: ' . $e->getMessage());
        }
    }

    function gatilhoTarefa(){
        /* 
        Dispara a tarefa criada no Windows Task Scheduler para checar a capacity (cluster Azure) manualmente.
        */
        try {
            $log = new Logger(self::LOG_AGENDADOR);
            $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
            
            $nomeTarefa = 'AzureCapacityScheduler';
            $comando = "schtasks /run /tn \"$nomeTarefa\"";
    
            exec($comando, $output, $returnVar);
    
            if ($returnVar === 0) {
                $log->info("Tarefa '$nomeTarefa' disparada com sucesso.");
            } else {
                $log->error("Erro ao disparar tarefa '$nomeTarefa'. Output: " . implode($output));
            }
        } catch (Exception $e) {
            $log->error('Erro ao disparar tarefa para checar capacidade: ' . $e->getMessage());
        }
    }
}