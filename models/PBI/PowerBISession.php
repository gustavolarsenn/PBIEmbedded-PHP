<?php

require_once __DIR__ . '/../../utils/config/config.php';

class PowerBISession {

    private $pdo;
    private $id_usuario;
    private const LOG_FILE = 'PowerBI';
    public function __construct($pdo, $id_usuario) {
        $this->pdo = $pdo;
        $this->id_usuario = $id_usuario;
    }

    public function criarSessaoPBI(){
        /* Cria sessão de usuário assim que ele acessa relatórios PowerBI. Caso ele já tiver sessão validar, renova por mais 1 hora */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $log->info('Verificando se usuário já possui sessão de PowerBI ativa', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
            $stmt = $this->pdo->prepare('SELECT * FROM SessaoPBI WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->bind_param('i', $this->id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $powerbi = [];

            while ($row = $result->fetch_assoc()) {
                $powerbi[] = $row;
            }

            $stmt->close();
    
            if (!$powerbi) {
                $log->info('Criando sessão de PowerBI', ['user' => $this->id_usuario]);
                $stmt = $this->pdo->prepare('INSERT INTO SessaoPBI (id_usuario) VALUES (?)');
                $stmt->bind_param('i', $this->id_usuario);
                $stmt->execute();
                $stmt->close();
                return;
            }
            $log->info('Renovando sessão de PowerBI', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
            $stmt = $this->pdo->prepare('UPDATE SessaoPBI SET data_validade = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->bind_param('i', $this->id_usuario);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            $log->error('Erro ao criar sessão de PowerBI: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
        }
    }

    public function inativarSessaoPBI(){
        /* Caso usuário deslogar, sua sessão de PowerBI será inativada. */ 
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('UPDATE SessaoPBI SET data_validade = NOW() WHERE id_usuario = ? AND data_validade > NOW()');
            $stmt->bind_param('i', $this->id_usuario);
            $stmt->execute();
            $stmt->close();

            $log->info('Sessão de PowerBI inativada com sucesso', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
        } catch (Exception $e) {
            $log->error('Erro ao inativar sessão de PowerBI: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
        }
    }
    
    public function sessoesAtivasPBI(){
        /* Verifica se existe alguma sessão PowerBI ativa */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $log->info('Verificando se existem sessões ativas de PowerBI', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
            $stmt = $this->pdo->prepare('SELECT * FROM SessaoPBI WHERE data_validade > NOW()');
            $stmt->execute();
            $result = $stmt->get_result();
            $powerbi = [];

            while ($row = $result->fetch_assoc()) {
                $powerbi[] = $row;
            }

            $stmt->close();

            if ($powerbi) {
                $log->info('Sessão de PowerBI ativa encontrada', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
                return true;
            }
            $log->info('Nenhuma sessão de PowerBI ativa', ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
            return false;
        } catch (Exception $e) {
            $log->error('Erro ao buscar sessões de PowerBI ativas: ' . $e->getMessage(), ['user' => $this->id_usuario, 'page' => $_SERVER['REQUEST_URI']]);
        }
    }
}