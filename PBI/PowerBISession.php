<?php

class PowerBISession {

    private $pdo;
    private $id_usuario;

    public function __construct($pdo, $id_usuario) {
        $this->pdo = $pdo;
        $this->id_usuario = $id_usuario;
    }

    public function criarSessaoPBI(){
        $stmt = $this->pdo->prepare('SELECT * FROM sessao_pbi WHERE id_usuario = ? AND data_validade > NOW()');
        $stmt->execute([$this->id_usuario]);
        $powerbi = $stmt->fetch();

        if (!$powerbi) {
            $stmt = $this->pdo->prepare('INSERT INTO sessao_pbi (id_usuario) VALUES (?)');
            $stmt->execute([$this->id_usuario]);
        } else {
            $stmt = $this->pdo->prepare('UPDATE sessao_pbi SET data_validade = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id_usuario = ?');
            $stmt->execute([$this->id_usuario]);
        }
    }

    public function sessoesAtivasPBI(){
        $stmt = $this->pdo->prepare('SELECT * FROM sessao_pbi WHERE id_usuario = ? AND data_validade > NOW()');
        $stmt->execute([$this->id_usuario]);
        $powerbi = $stmt->fetch();

        if ($powerbi) {
            return true;
        } else {
            return false;
        }
    }
}