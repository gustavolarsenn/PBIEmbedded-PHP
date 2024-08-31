<?php

require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\config\\EmailErrorHandler.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
    private const LOG = 'prancha';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
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
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        $emailErrorHandler = new EmailErrorHandler();
        $log->pushHandler($emailErrorHandler);
        try{
            $stmt = $pdo->prepare('SELECT DISTINCT navio FROM pranchareports ORDER BY CAST(periodo_inicial AS date) DESC');
            $stmt->execute();
            $log->info('Navios listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar navios', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['erro' => $e->getMessage()]);
        }
    }

    public function pegarDadosNavio($pdo, $navio) {
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        $emailErrorHandler = new EmailErrorHandler();
        $log->pushHandler($emailErrorHandler);
        try{
            $stmt = $pdo->prepare("SELECT navio, relatorio_no, ternos, periodo_inicial, periodo_final, 
            CONCAT(LPAD(HOUR(periodo_inicial), 2, 0), ':' ,RPAD(MINUTE(periodo_inicial), 2, 0), ' x ', LPAD(HOUR(periodo_final), 2, 0), ':' ,RPAD(MINUTE(periodo_final), 2, 0)) AS periodo,
            data, TIME_TO_SEC(duracao) AS duracao, TIME_TO_SEC(chuva) AS chuva, TIME_TO_SEC(transporte) AS transporte, TIME_TO_SEC(forca_maior) AS forca_maior, TIME_TO_SEC(outros) AS outros, TIME_TO_SEC(horas_operacionais) AS horas_operacionais, volume, meta, observacao FROM pranchareports WHERE navio = :navio");
            $stmt->execute([':navio' => $navio]);
            $log->info('Dados do navio listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar dados do navio', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}