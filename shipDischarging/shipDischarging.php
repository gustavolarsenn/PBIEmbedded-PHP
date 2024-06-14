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
    $query = 'SELECT * FROM shipdischarging WHERE 1=1';
    if ($column_agg && $type_agg) {
        // Whitelist known good values for column_agg and type_agg
        $allowed_aggregations = ['SUM', 'COUNT', 'AVG', 'MIN', 'MAX']; // standard SQL aggregations
        
        if (in_array($type_agg, $allowed_aggregations)) {
            $query = "SELECT $select $type_agg($column_agg) AS $column_agg FROM shipdischarging WHERE 1=1";
        }
    }

    if ($where) {
        $json_where = json_decode($where);
        if ($json_where->navio) $query .= " AND navio = :navio";
        if ($json_where->data) $query .= " AND data = :data";
        if ($json_where->periodo) $query .= " AND periodo = :periodo";
        if ($json_where->cliente) $query .= " AND cliente = :cliente";
        if ($json_where->porao) $query .= " AND porao = :porao";
        if ($json_where->armazem) $query .= " AND armazem = :armazem";
        if ($json_where->produto) $query .= " AND produto = :produto";
        if ($json_where->di) $query .= " AND di = :di";
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
        if ($json_where->navio) $stmt->bindParam(':navio', $json_where->navio);
        if ($json_where->data) $stmt->bindParam(':data', $json_where->data);
        if ($json_where->periodo) $stmt->bindParam(':periodo', $json_where->periodo);
        if ($json_where->cliente) $stmt->bindParam(':cliente', $json_where->cliente);
        if ($json_where->porao) $stmt->bindParam(':porao', $json_where->porao);
        if ($json_where->armazem) $stmt->bindParam(':armazem', $json_where->armazem);
        if ($json_where->produto) $stmt->bindParam(':produto', $json_where->produto);
        if ($json_where->di) $stmt->bindParam(':di', $json_where->di);
    }

    // print_r($query);
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

    public function pegarUnicos($pdo, $campo, $where){
        $query = 'SELECT DISTINCT ' . $campo . ' FROM shipdischarging WHERE 1=1';

        $query = $this->concatWhere($query, $where, 'filter');

        if ($campo == 'navio') $query = $query . ' ORDER BY CAST(data AS date) DESC';
        else $query = $query . ' ORDER BY ' . $campo . ' ASC';
        
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, 'filter');
        $stmt->execute();

        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function concatWhere($query, $where, $type){
        $json_where = json_decode($where);
        if ($json_where->navio) $query .= " AND navio = :navio";
        if ($json_where->cliente) $query .= " AND cliente IN(:cliente)";
        if ($json_where->armazem) $query .= " AND armazem = :armazem";
        if ($json_where->produto) $query .= " AND produto = :produto";
        if ($json_where->di) $query .= " AND di = :di";

        if ($type == 'planejado'){
            if ($json_where->data) $query .= " AND data = :data";
            if ($json_where->periodo) $query .= " AND periodo = :periodo";
            if ($json_where->porao) $query .= " AND porao = :porao";
        }

        if ($type == 'filter'){
            if ($json_where->peso) $query .= " AND peso > :peso";
        }

        return $query;
    }

    public function bindWhere($stmt, $where, $type){
        $json_where = json_decode($where);

        if ($json_where->navio) $stmt->bindParam(':navio', $json_where->navio);
        if ($json_where->cliente) $stmt->bindParam(':cliente', $json_where->cliente);
        if ($json_where->armazem) $stmt->bindParam(':armazem', $json_where->armazem);
        if ($json_where->produto) $stmt->bindParam(':produto', $json_where->produto);
        if ($json_where->di) $stmt->bindParam(':di', $json_where->di);

        if ($type == 'planejado'){
            if ($json_where->data) $stmt->bindParam(':data', $json_where->data);
            if ($json_where->periodo) $stmt->bindParam(':periodo', $json_where->periodo);
            if ($json_where->porao) $stmt->bindParam(':porao', $json_where->porao);
        }

        if ($type == 'filter'){
            if ($json_where->peso) $stmt->bindParam(':peso', $json_where->peso);
        }
        return $stmt;
    }

    public function totalDescarregado($pdo, $where, $type = 'realizado'){
        $query = 'SELECT SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetch()]);
    }

    public function totalPlanejado($pdo, $where, $type = 'realizado'){
        $query = 'SELECT sum(planejado) AS planejado FROM shipplanned WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetch()]);
    }

    public function descarregadoClienteArmazemDI($pdo, $where, $type = 'realizado'){
        $query_realizado = 'SELECT cliente, armazem, di, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query_realizado = $this->concatWhere($query_realizado, $where, $type);
        $query_realizado .= ' GROUP BY cliente, armazem, di';

        $query_planejado = 'SELECT cliente, armazem, di, SUM(planejado) AS planejado FROM shipplanned WHERE 1=1';
        $query_planejado = $this->concatWhere($query_planejado, $where, $type);
        $query_planejado .= ' GROUP BY cliente, armazem, di';

        $query = 'SELECT realizado.cliente, realizado.armazem, realizado.di, realizado.peso, planejado.planejado FROM 
                (' . $query_realizado . ') AS realizado 
                LEFT JOIN (' . $query_planejado . ') AS planejado 
                ON realizado.cliente = planejado.cliente AND realizado.armazem = planejado.armazem AND realizado.di = planejado.di 
                ORDER BY realizado.peso / planejado.planejado DESC';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoPorao($pdo, $where, $type = 'realizado'){
        $query = 'SELECT porao, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $query .= ' GROUP BY porao';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoDia($pdo, $where, $type = 'realizado'){
        $query = 'SELECT CAST(data AS date) AS data, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $query .= ' GROUP BY CAST(data AS date)';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoCliente($pdo, $where, $type = 'realizado'){
        $query = 'SELECT cliente, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $query .= ' GROUP BY cliente ORDER BY SUM(peso) DESC';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoDiaPeriodo($pdo, $where, $type = 'realizado'){
        $query = 'SELECT CAST(data AS date) AS data, periodo, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhere($query, $where, $type);
        $query .= ' GROUP BY CAST(data AS date), periodo';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhere($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }
}