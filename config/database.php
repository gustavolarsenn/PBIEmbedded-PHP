<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "zport-db";
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // LOGAR ERRO
            // echo json_encode(["mensagem" => "Falha ao conectar com banco de dados: " . $e->getMessage()]); 
            header('Location: /views/erro/erro_db.php');
            // echo json_encode(["mensagem" => "Falha ao conectar com banco de dados: " . $e->getMessage()]);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}