<?php
require_once __DIR__ . '/../utils/config/config.php';

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

    public function pegarInfoNavio($pdo, $viagem){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $stmt = $pdo->prepare("SELECT navio, data, produto, porto, berco, volume_manifestado, modalidade, prancha_minima FROM Navio WHERE id = ?");
            $stmt->bind_param('s', $viagem);
            $stmt->execute();
            $result = $stmt->get_result();
            $navio_final = [];
            while ($row = $result->fetch_assoc()) {
                $navio_final[] = $row;
            }

            $log->info('Informações do navio listadas', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $navio_final]);
        } catch (Exception $e) {
            $log->error("Exceção ao listar informações do navio", ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function pegarNaviosUnicos($pdo){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $stmt = $pdo->prepare("SELECT DISTINCT id As id_viagem, CONCAT(id, ' - ', navio) AS navio FROM Navio ORDER BY CAST(data AS date) DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            $navios = [];

            while ($row = $result->fetch_assoc()) {
                $navios[] = $row;
            }

            $stmt->close();
            $log->info('Navios listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['data' => $navios]);
        } catch (Exception $e) {
            $log->error('Exceção ao listar navios', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['erro' => $e->getMessage()]);
        }
    }
}