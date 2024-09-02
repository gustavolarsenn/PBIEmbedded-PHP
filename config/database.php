<?php

require_once __DIR__ . '\\config.php';

require_once CAMINHO_BASE . '\\config\\AppLogger.php';

class Database {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    private const LOG_FILE = 'Database';

    public function __construct() {
        $this->servername = getenv('DB_HOST');
        $this->username = getenv('DB_USER');
        $this->password = "";
        $this->dbname = getenv('DB_NAME');
        $this->conn = null;
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $log->info("Contecando-se ao banco de dados", ['user' => isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] :  null, 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $log->error("Exceção ao conectar com o banco de dados", ['user' => isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] :  null, 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            header('Location: /views/erro/erro_db.php');
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}