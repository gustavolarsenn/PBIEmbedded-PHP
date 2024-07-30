<?php
    class DescarregamentoNavio{
        private $pdo;
        private $no;
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

        public function __construct($pdo, $no, $ticket, $placa, $peso, $data, $periodo, $cliente, $porao, $armazem, $cliente_armazem_lote_di_produto, $produto, $observacao){
            $this->pdo = $pdo;
            $this->no = $no;
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
            $stmt = $this->pdo->prepare('INSERT INTO shipdischarging (ticket, placa, peso, data, periodo, cliente, porao, armazem, cliente_armazem_lote_di_produto, produto, observacao) VALUES (:ticket, :placa, :peso, :data, :periodo, :cliente, :porao, :armazem, :cliente_armazem_lote_di_produto, :produto, :observacao)');
            $stmt->execute([
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
    }

    public static function buscarTodosDados($pdo){
        $stmt = $pdo->prepare('SELECT * FROM shipdischarging');
        $stmt->execute();
        // return $stmt->fetchAll();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function buscarRegistroPorNum(){
        $stmt = $this->pdo->prepare('SELECT * FROM shipdischarging WHERE no = :no');
        $stmt->execute([':no' => $this->no]);
        return $stmt->fetch();
    }

    public function pegarDadosNavioRealizado($pdo, $navio){
        $stmt = $pdo->prepare('SELECT * FROM shipdischarging WHERE navio = :navio');
        $stmt->bindParam(':navio', $navio);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll(), 'message' => 'Dados do navio realizado']);
    }

    public function pegarDadosNavioPlanejado($pdo, $navio){
        $stmt = $pdo->prepare('SELECT * FROM shipplanned WHERE navio = :navio');
        $stmt->bindParam(':navio', $navio);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll(), 'message' => 'Dados do navio planejado']);
    }

    public function pegarNaviosUnicos($pdo){
        try {
            $stmt = $pdo->prepare('SELECT DISTINCT navio FROM shipdischarging ORDER BY CAST(data AS date) DESC');
            $stmt->execute();
            return json_encode(['data' => $stmt->fetchAll(), 'message' => 'Navios únicos']);
        } catch (PDOException $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}