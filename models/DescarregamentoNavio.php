<?php

require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
        private const LOG = 'descarregamento_navio';
        private const LOG_FILE = 'DescarregamentoNavio';
        private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
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
            $log = new Logger(self::LOG);
            $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
            try {
                $stmt = $this->pdo->prepare('INSERT INTO shipdischarging (navio, ticket, placa, peso, data, periodo, cliente, porao, armazem, cliente_armazem_lote_di_produto, produto, observacao) VALUES (:ticket, :placa, :peso, :data, :periodo, :cliente, :porao, :armazem, :cliente_armazem_lote_di_produto, :produto, :observacao)');
                $stmt->execute([
                    'navio' => $this->navio,
                    'ticket' => $this->ticket,
                    'placa' => $this->placa,
                    'peso' => $this->peso,
                    'data' => $this->data,
                    'periodo' => $this->periodo,
                    'cliente' => $this->cliente,
                    'porao' => $this->porao,
                    'armazem' => $this->armazem,
                    'cliente_armazem_lote_di_produto' => $this->cliente_armazem_lote_di_produto,
                    'produto' => $this->produto,
                    'observacao' => $this->observacao
                ]);
                $log->info('Registro de Descarregamento de Navio criado', ['user' => $_SESSION['id_usuario'], 'navio' => $this->navio, 'ticket' => $this->ticket, 'page' => $_SERVER['HTTP_REFERER']]);
            } catch (Exception $e) {
                $log->error('Exceção ao criar registro de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'navio' => $this->navio, 'ticket' => $this->ticket, 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
                return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
            }
    }

    public static function buscarTodosDados($pdo){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $pdo->prepare('SELECT * FROM shipdischarging');
            $stmt->execute();
            $log->info('Todos os registros de Descarregamento de Navio listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar todos os registros de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function buscarRegistroPorNum(){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM shipdischarging WHERE no = :no');
            $stmt->execute([':no' => $this->no]);
            $log->info('Registro ' . $this->no . ' de Descarregamento de Navio listado', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            $log->error('Exceção ao listar registro ' . $this->no . ' de Descarregamento de Navio', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavioRealizado($pdo, $navio){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $pdo->prepare('SELECT * FROM shipdischarging WHERE navio = :navio');
            $stmt->bindParam(':navio', $navio);
            $stmt->execute();
            $navioRealizado = $stmt->fetchAll();
            $log->info('Dados do navio de Realizado listados', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $navioRealizado, 'mensagem' => 'Dados do navio realizado']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados de Realizado do navio', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavioPlanejado($pdo, $navio){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $pdo->prepare('SELECT * FROM shipplanned WHERE navio = :navio');
            $stmt->bindParam(':navio', $navio);
            $stmt->execute();
            $navioPlanejado = $stmt->fetchAll();
            $log->info('Dados do navio de Planejado listados', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $navioPlanejado, 'mensagem' => 'Dados do navio planejado']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados de Planejado do navio', ['user' => $_SESSION['id_usuario'], 'navio' => $navio, 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function pegarNaviosUnicos($pdo){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $pdo->prepare('SELECT DISTINCT navio FROM shipdischarging ORDER BY CAST(data AS date) DESC');
            $stmt->execute();
            $naviosUnicos = $stmt->fetchAll();
            $log->info('Navios listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $naviosUnicos, 'message' => 'Navios únicos']);
        } catch (Exception $e) {
            $log->error('Exceção ao listar navios', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}