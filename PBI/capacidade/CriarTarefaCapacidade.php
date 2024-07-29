<?php

require_once '../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function criarArquivoBat($nomeArquivo){
    $log = new Logger('tarefa_gerenciamento_capacidade');
    $log->pushHandler(new StreamHandler(__DIR__ . '/tarefa_gerenciamento_capacidade.log', Logger::DEBUG));
    try {        
        if (!file_exists($nomeArquivo)) {
            $conteudo = '@echo off' . PHP_EOL;
            $conteudo .= 'start /min php ' . __DIR__ . '\AgendadorCapacidade.php' . PHP_EOL;
            $conteudo .= 'exit'; 

            file_put_contents(__DIR__ . '\\' . $nomeArquivo, $conteudo);

            $log->info('Arquivo bat criado com sucesso.');

            return;
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
        $log = new Logger('tarefa_gerenciamento_capacidade');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tarefa_gerenciamento_capacidade.log', Logger::DEBUG));
        
        $nomeArquivoBat = 'capacity.bat';

        criarArquivoBat($nomeArquivoBat);
        
        
        $caminhoScript = __DIR__ . '\\' . $nomeArquivoBat;
        $nomeTarefa = 'AzureCapacityScheduler';
        $periodicidade = 'minute';
        $intervalo = 10;
        
        // Verifica se a tarefa existe
        $verificarTarefaExiste = "schtasks /query /tn \"$nomeTarefa\"";
        exec($verificarTarefaExiste, $output, $returnVar);
        
        if($returnVar === 0){
            $log->info("A tarefa '$nomeTarefa' já existe.");
        } else {
            // Cria a tarefa
            // $comando = "schtasks /create /tn \"$nomeTarefa\" /tr \"$caminhoPHP $caminhoScript\" /sc $periodicidade /mo $intervalo /f";
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