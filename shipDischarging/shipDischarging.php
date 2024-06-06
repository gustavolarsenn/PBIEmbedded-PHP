<?php
    class ShipDischarging{
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

        public function create(){
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

    public static function readAll($pdo){
        $stmt = $pdo->prepare('SELECT * FROM shipdischarging');
        $stmt->execute();
        // return $stmt->fetchAll();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function readQuery($pdo, $select, $group_by, $order_by, $limit, $where, $column_agg, $type_agg){
    $query = 'SELECT * FROM shipdischarging';
    if ($column_agg && $type_agg) {
        // Whitelist known good values for column_agg and type_agg
        // $allowed_columns = ['cliente', 'peso', 'ticket', 'data', 'armazem', 'porao', 'periodo', 'cliente_armazem_lote_di_produto', 'produto', 'CONCAT(MONTH(data), "/", YEAR(data)), periodo']; // replace with your actual column names
        $allowed_aggregations = ['SUM', 'COUNT', 'AVG', 'MIN', 'MAX']; // standard SQL aggregations
        
        if (in_array($type_agg, $allowed_aggregations)) {
            $query = "SELECT $select $type_agg($column_agg) AS $column_agg FROM shipdischarging";
        }
    }

    if ($where) {
        $query .= " WHERE $where = :where";
    }

    if ($group_by) {
        // Whitelist known good values for group_by
        if ($group_by) {
            $query .= " GROUP BY $group_by";
        }
    }

    if ($order_by) {
        // Whitelist known good values for order_by
        if ($order_by) {
            $query .= " ORDER BY $order_by";
        }
    }

    if ($limit) {
        $query .= " LIMIT :limit";
    }

    $stmt = $pdo->prepare($query);

    if ($where) {
        $stmt->bindParam(':where', $where);
    }

    if ($limit) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    return json_encode(['data' => $stmt->fetchAll()]);
    }


    public function read(){
        $stmt = $this->pdo->prepare('SELECT * FROM shipdischarging WHERE no = :no');
        $stmt->execute([':no' => $this->no]);
        return $stmt->fetch();
    }
}