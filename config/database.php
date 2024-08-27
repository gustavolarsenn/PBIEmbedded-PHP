<?php

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "zport-db";
    private $conn;
    private const LOG_FILE = 'Database';
    private const LOG = 'database';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';


    public function __construct() {
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $log->info("Contecando-se ao banco de dados", ['user' => isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] :  null, 'page' => $_SERVER['HTTP_REFERER']]);
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // LOGAR ERRO
            $log->info("Exceção ao conectar com o banco de dados", ['user' => isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] :  null, 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            header('Location: /views/erro/erro_db.php');
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}