<?php

require_once __DIR__ . '/../config/config.php';

class Prancha {
    private $pdo;
    private $navio;
    private $relatorio_no;
    private $ternos;
    private $periodo_inicial;
    private $periodo_final;
    private $data;
    private $duracao;
    private $chuva;
    private $transporte;
    private $forca_maior;
    private $outros;
    private $horas_operacionais;
    private $volume;
    private $meta;
    private $observacao;
    private const LOG_FILE = 'Prancha';
    public function __construct($pdo, $navio, $relatorio_no, $ternos, $periodo_inicial, $periodo_final, $data, $duracao, $chuva, $transporte, $forca_maior, $outros, $horas_operacionais, $volume, $meta, $observacao){
        $this->pdo = $pdo;
        $this->navio = $navio;
        $this->relatorio_no = $relatorio_no;
        $this->ternos = $ternos;
        $this->periodo_inicial = $periodo_inicial;
        $this->periodo_final = $periodo_final;
        $this->data = $data;
        $this->duracao = $duracao;
        $this->chuva = $chuva;
        $this->transporte = $transporte;
        $this->forca_maior = $forca_maior;
        $this->outros = $outros;
        $this->horas_operacionais = $horas_operacionais;
        $this->volume = $volume;
        $this->meta = $meta;
        $this->observacao = $observacao;
    }
    
    public function pegarNaviosUnicos($pdo){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $stmt = $pdo->prepare('SELECT DISTINCT navio FROM ControlePrancha ORDER BY CAST(periodo_inicial AS date) DESC');
            $stmt->execute();
            $log->info('Navios listados', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar navios', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavio($pdo, $navio) {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $stmt = $pdo->prepare("SELECT navio, relatorio_no, ternos, periodo_inicial, periodo_final, 
            CONCAT(LPAD(HOUR(periodo_inicial), 2, 0), ':' ,RPAD(MINUTE(periodo_inicial), 2, 0), ' x ', LPAD(HOUR(periodo_final), 2, 0), ':' ,RPAD(MINUTE(periodo_final), 2, 0)) AS periodo,
            data, TIME_TO_SEC(duracao) AS duracao, TIME_TO_SEC(chuva) AS chuva, TIME_TO_SEC(transporte) AS transporte, TIME_TO_SEC(forca_maior) AS forca_maior, TIME_TO_SEC(outros) AS outros, TIME_TO_SEC(horas_operacionais) AS horas_operacionais, volume, meta, observacao FROM ControlePrancha WHERE navio = :navio");
            $stmt->execute([':navio' => $navio]);
            $log->info('Dados do navio listados', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados do navio', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}