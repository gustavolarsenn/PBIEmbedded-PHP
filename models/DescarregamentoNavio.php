<?php

require_once __DIR__ . '/../config/config.php';

    class DescarregamentoNavio{
        private $pdo;
        private $no;
        private $navio;
        private $ticket;
        private $placa;
        private $peso;
        private $data;
        private $periodo;
        private $cliente;
        private $porao;
        private $armazem;
        private $cliente_armazem_lote_di_produto;
        private $produto;
        private $observacao;
        private const LOG_FILE = 'DescarregamentoNavio';
        public function __construct($pdo, $no, $navio, $ticket, $placa, $peso, $data, $periodo, $cliente, $porao, $armazem, $cliente_armazem_lote_di_produto, $produto, $observacao){
            $this->pdo = $pdo;
            $this->no = $no;
            $this->navio = $navio;
            $this->ticket = $ticket;
            $this->placa = $placa;
            $this->peso = $peso;
            $this->data = $data;
            $this->periodo = $periodo;
            $this->cliente = $cliente;
            $this->porao = $porao;
            $this->armazem = $armazem;
            $this->cliente_armazem_lote_di_produto = $cliente_armazem_lote_di_produto;
            $this->produto = $produto;
            $this->observacao = $observacao;
        }

        public function criarRegistro(){
            $log = AppLogger::getInstance(self::LOG_FILE);
            try {
                $stmt = $this->pdo->prepare('INSERT INTO DescarregamentoNavio (navio, ticket, placa, peso, data, periodo, cliente, porao, armazem, cliente_armazem_lote_di_produto, produto, observacao) VALUES (:ticket, :placa, :peso, :data, :periodo, :cliente, :porao, :armazem, :cliente_armazem_lote_di_produto, :produto, :observacao)');
                $stmt->bind_param('sssdsssissss', $this->navio, $this->ticket, $this->placa, $this->peso, $this->data, $this->periodo, $this->cliente, $this->porao, $this->armazem, $this->cliente_armazem_lote_di_produto, $this->produto, $this->observacao);
                $stmt->execute();
                $stmt->close();
                $log->info('Registro de Descarregamento de Navio criado', ['user' => $_SESSION['id_usuario'], 'navio' => $this->navio, 'ticket' => $this->ticket, 'page' => $_SERVER['REQUEST_URI']]);
            } catch (Exception $e) {
                $log->error('Exceção ao criar registro de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'navio' => $this->navio, 'ticket' => $this->ticket, 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
                return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
            }
    }

    public static function buscarTodosDados($pdo){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $pdo->prepare('SELECT * FROM DescarregamentoNavio');
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $dadosDescarragamentoNavio[] = $row;
            }

            $stmt->close();
            $log->info('Todos os registros de Descarregamento de Navio listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $dadosDescarragamentoNavio]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar todos os registros de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function buscarRegistroPorNum(){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM DescarregamentoNavio WHERE no = ?');
            $stmt->bind_param('i', $this->no);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $dadosDescarragamentoNavio[] = $row;
            }

            $stmt->close();
            $log->info('Registro ' . $this->no . ' de Descarregamento de Navio listado', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return $dadosDescarragamentoNavio;
        } catch (Exception $e) {
            $log->error('Exceção ao listar registro ' . $this->no . ' de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavioRealizado($pdo, $navio){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $pdo->prepare('SELECT * FROM DescarregamentoNavio WHERE navio = ?');
            $stmt->bind_param('s', $navio);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $navioRealizado[] = $row;
            }

            $stmt->close();
            $log->info('Dados do navio de Realizado listados', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $navioRealizado, 'mensagem' => 'Dados do navio realizado']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados de Realizado do navio', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavioPlanejado($pdo, $navio){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $pdo->prepare('SELECT * FROM DescarregamentoNavioPlanejado WHERE navio = ?');
            $stmt->bind_param('s', $navio);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $navioPlanejado[] = $row;
            }

            $stmt->close();
            
            $log->info('Dados do navio de Planejado listados', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $navioPlanejado, 'mensagem' => 'Dados do navio planejado']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados de Planejado do navio', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarNaviosUnicos($pdo){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $pdo->prepare('SELECT DISTINCT navio FROM DescarregamentoNavio ORDER BY CAST(data AS date) DESC');
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $naviosUnicos[] = $row;
            }

            $stmt->close();

            $log->info('Navios listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $naviosUnicos, 'message' => 'Navios únicos']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar navios', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}