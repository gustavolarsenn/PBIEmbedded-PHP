<?php
require_once __DIR__ . '\\..\config\\config.php';

class Navio {
    private $pdo;
    private $navio;
    private $data;
    private $produto;
    private $berco;
    private $volume_manifestado;
    private $modalidade;
    private $prancha_minima;
    private const LOG_FILE = 'Navio';
    public function __construct($pdo, $navio, $data, $produto, $berco, $volume_manifestado, $modalidade, $prancha_minima){
        $this->pdo = $pdo;
        $this->navio = $navio;
        $this->data = $data;
        $this->produto = $produto;
        $this->berco = $berco;
        $this->volume_manifestado = $volume_manifestado;
        $this->modalidade = $modalidade;
        $this->prancha_minima = $prancha_minima;
    }

    public function pegarInfoNavio($pdo, $navio){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $stmt = $pdo->prepare("SELECT navio, data, produto, berco, volume_manifestado, modalidade, prancha_minima FROM navio WHERE navio = :navio");
            $stmt->execute([':navio' => $navio]);
            $log->info('Informações do navio listadas', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error("Exceção ao listar informações do navio", ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}