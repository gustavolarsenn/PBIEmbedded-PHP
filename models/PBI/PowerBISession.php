<?php

require_once __DIR__ . '\\..\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class PowerBISession {

    private $pdo;
    private $id_usuario;
    private const LOG_FILE = 'PowerBI';
    private const LOG = 'sessao_pbi';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
    
    public function __construct($pdo, $id_usuario) {
        $this->pdo = $pdo;
        $this->id_usuario = $id_usuario;
    }

    public function criarSessaoPBI(){
        /* Cria sessão de usuário assim que ele acessa relatórios PowerBI. Caso ele já tiver sessão validar, renova por mais 1 hora */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $log->info('Verificando se usuário já possui sessão de PowerBI ativa', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
            $stmt = $this->pdo->prepare('SELECT * FROM sessao_pbi WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->execute([$this->id_usuario]);
            $powerbi = $stmt->fetch();
    
            if (!$powerbi) {
                $log->info('Criando sessão de PowerBI', ['user' => $this->id_usuario]);
                $stmt = $this->pdo->prepare('INSERT INTO sessao_pbi (id_usuario) VALUES (?)');
                $stmt->execute([$this->id_usuario]);
                return;
            }
            $log->info('Renovando sessão de PowerBI', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
            $stmt = $this->pdo->prepare('UPDATE sessao_pbi SET data_validade = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->execute([$this->id_usuario]);
        } catch (Exception $e) {
            $log->error('Erro ao criar sessão de PowerBI: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
        }
    }

    public function inativarSessaoPBI(){
        /* Caso usuário deslogar, sua sessão de PowerBI será inativada. */ 
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('UPDATE sessao_pbi SET data_validade = NOW() WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->execute([$this->id_usuario]);

            $log->info('Sessão de PowerBI inativada com sucesso', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
        } catch (Exception $e) {
            $log->error('Erro ao inativar sessão de PowerBI: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
        }
    }
    
    public function sessoesAtivasPBI(){
        /* Verifica se existe alguma sessão PowerBI ativa */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $log->info('Verificando se existem sessões ativas de PowerBI', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
            $stmt = $this->pdo->prepare('SELECT * FROM sessao_pbi WHERE data_validade > NOW()');
            $stmt->execute();
            $powerbi = $stmt->fetch();
            
            if ($powerbi) {
                $log->info('Sessão de PowerBI ativa encontrada', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
                return true;
            }
            $log->info('Nenhuma sessão de PowerBI ativa', ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
            return false;
        } catch (Exception $e) {
            $log->error('Erro ao buscar sessões de PowerBI ativas: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['HTTP_REFERER']]);
        }
    }
}