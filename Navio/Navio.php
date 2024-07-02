<?php

class Navio {
    private $pdo;
    private $navio;
    private $data;
    private $produto;
    private $berco;
    private $volume_manifestado;
    private $modalidade;
    private $prancha_minima;

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
        try{
            $stmt = $pdo->prepare("SELECT navio, data, produto, berco, volume_manifestado, modalidade, prancha_minima FROM navio WHERE navio = :navio");
            $stmt->execute([':navio' => $navio]);
            return json_encode(['data' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}