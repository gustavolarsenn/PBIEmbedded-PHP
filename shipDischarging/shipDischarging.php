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

    public function read(){
        $stmt = $this->pdo->prepare('SELECT * FROM shipdischarging WHERE no = :no');
        $stmt->execute([':no' => $this->no]);
        return $stmt->fetch();
    }

    // public function pegarUnicos($pdo, $campo, $where){
    //     $query = 'SELECT DISTINCT ' . $campo . ' FROM shipdischarging WHERE 1=1';

    //     $query = $this->concatWhereArray($query, $where, 'filter');

    //     if ($campo == 'navio') $query =  $query . ' ORDER BY CAST(data AS date) DESC';
    //     else $query = $query . ' ORDER BY ' . $campo . ' ASC';
    //     // $query = $query . ' ORDER BY ' . $campo . ' ASC';

    //     $stmt = $pdo->prepare($query);
    //     $stmt = $this->bindWhereArray($stmt, $where, 'filter');
    //     $stmt->execute();
    //     return json_encode(['data' => $stmt->fetchAll()]);
    // }
    public function pegarUnicos($pdo, $campo, $where){
        // QUANDO MUDO A ORDEM DO ORDER BY, PARA SER ADICIONADO DEPOIS, DA ERRO!!!
        $query = 'SELECT DISTINCT ' . $campo . ' FROM shipdischarging WHERE 1=1 ORDER BY ' . $campo . ' ASC';

        if ($campo === 'navio') {
            $query = $this->concatWhereArray($query, $where, 'filter');
        } else {
            $query = $this->concatWhereArray($query, $where, 'filter');
        }

        try {
            $stmt = $pdo->prepare($query);
            $stmt = $this->bindWhereArray($stmt, $where, 'filter');
            $stmt->execute();
    
            return json_encode(['data' => $stmt->fetchAll(), 'query' => $query]);
        } catch (PDOException $e) {
            return json_encode(['message' => $e->getMessage(), 'query' => $query]);
        }

    }

    public function loopThroughWhere($campo, $array){
        $where = ' AND ' . $campo . ' IN (';
        $placeholders = []; // Initialize placeholders array outside the loop

        // foreach ($array as $key => $values) {
        //     if (!empty($values)) {
                // Assuming $values is an array and you want to create a placeholder for each value
                foreach ($array as $index => $value) {
                    $placeholders[] = ':' . $campo . $index; // Create a unique placeholder for each value
                }
            // }
        // }

        // Use implode to correctly format the string with commas
        $where .= implode(', ', $placeholders);

        $where .= ')'; // Close the IN clause
        // Additional conditions based on $type can be added here
        return $where;
    }

    public function concatWhereArray($query, $where, $type){
        $json_where = json_decode($where);

        // Iterate through the decoded array and remove extra quotes
        array_walk_recursive($json_where, function(&$item, $key) {
            if (is_string($item)) {
                // Trim single quotes from the beginning and end of the string
                $item = trim($item, "'");
            }
        });
        
        if ($json_where->navio) $query .= $this->loopThroughWhere('navio', $json_where->navio);
        if ($json_where->cliente) $query .= $this->loopThroughWhere('cliente', $json_where->cliente);
        if ($json_where->armazem) $query .= $this->loopThroughWhere('armazem', $json_where->armazem);
        if ($json_where->produto) $query .= $this->loopThroughWhere('produto', $json_where->produto);
        if ($json_where->di) $query .= $this->loopThroughWhere('di', $json_where->di);
        
        if ($type !== 'planejado') {
            if ($json_where->periodo) $query .= $this->loopThroughWhere('periodo', $json_where->periodo);
            if ($json_where->porao) $query .= $this->loopThroughWhere('porao', $json_where->porao);
            if ($json_where->data) $query .= $this->loopThroughWhere('data', $json_where->data);
        } 
        
        if ($type === 'filter'){
            if ($json_where->peso) $query .= " AND peso > :peso";
        }

        return $query;
    }

    public function bindWhereArray($stmt, $where, $type){
        $json_where = json_decode($where);

        // Iterate through the decoded array and remove extra quotes
        array_walk_recursive($json_where, function(&$item, $key) {
            if (is_string($item)) {
                // Trim single quotes from the beginning and end of the string
                $item = trim($item, "'");
            }
        });
        
        if ($type === 'planejado') {
            unset($json_where->periodo);
            unset($json_where->porao);
            unset($json_where->data);
        }
        foreach ($json_where as $key => $values) {
            if (!empty($values)) {
                foreach ($values as $index => $value) {
                    $stmt->bindParam(':' . $key . $index, $value);
                }
            }

        }

        // if ($type === 'filter') {
        //     $stmt->bindParam(':peso', $json_where->peso);
        // }
        // Additional bindings based on $type can be added here
        return $stmt;
    }

    public function totalDescarregado($pdo, $where, $type = 'realizado'){
        try {
            $query = 'SELECT SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
            $query = $this->concatWhereArray($query, $where, $type);
            $stmt = $pdo->prepare($query);
            $stmt = $this->bindWhereArray($stmt, $where, $type);
            $stmt->execute();
            return json_encode(['data' => $stmt->fetch(), 'query' => $query]);
        } catch (PDOException $e) {
            return json_encode(['query' => $query]);
        }
    }

    public function totalPlanejado($pdo, $where, $type = 'planejado'){
        $query = 'SELECT sum(planejado) AS planejado FROM shipplanned WHERE 1=1';
        $query = $this->concatWhereArray($query, $where, $type);
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        // print_r($stmt);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetch()]);
    }

    public function descarregadoClienteArmazemDI($pdo, $where, $type = 'realizado'){
        $query_realizado = 'SELECT cliente, armazem, di, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query_realizado = $this->concatWhereArray($query_realizado, $where, 'realizado');
        $query_realizado .= ' GROUP BY cliente, armazem, di';

        $query_planejado = 'SELECT cliente, armazem, di, SUM(planejado) AS planejado FROM shipplanned WHERE 1=1';
        $query_planejado = $this->concatWhereArray($query_planejado, $where, 'planejado');
        $query_planejado .= ' GROUP BY cliente, armazem, di';

        $query = 'SELECT realizado.cliente, realizado.armazem, realizado.di, realizado.peso, planejado.planejado FROM 
                (' . $query_realizado . ') AS realizado 
                LEFT JOIN (' . $query_planejado . ') AS planejado 
                ON realizado.cliente = planejado.cliente AND realizado.armazem = planejado.armazem AND realizado.di = planejado.di 
                ORDER BY realizado.peso / planejado.planejado DESC';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoPorao($pdo, $where, $type = 'realizado'){
        $query = 'SELECT porao, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhereArray($query, $where, $type);
        $query .= ' GROUP BY porao';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoDia($pdo, $where, $type = 'realizado'){
        $query = 'SELECT CAST(data AS date) AS data, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhereArray($query, $where, $type);
        $query .= ' GROUP BY CAST(data AS date)';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoCliente($pdo, $where, $type = 'realizado'){
        $query = 'SELECT cliente, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhereArray($query, $where, $type);
        $query .= ' GROUP BY cliente ORDER BY SUM(peso) DESC';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }

    public function descarregadoDiaPeriodo($pdo, $where, $type = 'realizado'){
        $query = 'SELECT CAST(data AS date) AS data, periodo, SUM(peso) AS peso FROM shipdischarging WHERE 1=1';
        $query = $this->concatWhereArray($query, $where, $type);
        $query .= ' GROUP BY CAST(data AS date), periodo';
        $stmt = $pdo->prepare($query);
        $stmt = $this->bindWhereArray($stmt, $where, $type);
        $stmt->execute();
        return json_encode(['data' => $stmt->fetchAll()]);
    }
}