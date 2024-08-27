<?php

require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TipoUsuario{
    private $pdo;
    private $tipo;
    private $descricao;
    private const LOG = 'TipoUsuario';
    private const LOG_FILE = 'tipo_usuario';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';
    
    public function __construct($pdo, $tipo, $descricao){
        $this->pdo = $pdo;
        $this->tipo = $tipo;
        $this->descricao = $descricao;
    }

    public function pegarTipos(){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('SELECT DISTINCT id, tipo FROM tipo_usuario');
            $stmt->execute();
            $tipo_usuarios =  $stmt->fetchAll();

            $log->info('Tipos de usuário listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);

            return json_encode($tipo_usuarios);
        } catch (Exception $e) {
            $log->error('Erro ao listar tipos de usuário', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'erro' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }
}