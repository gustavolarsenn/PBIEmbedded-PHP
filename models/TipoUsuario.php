<?php

require_once __DIR__ . '/../config/config.php';

require_once CAMINHO_BASE . '/vendor/autoload.php';

class TipoUsuario{
    private $pdo;
    private $tipo;
    private $descricao;
    private const LOG_FILE = 'tipo_usuario';
    public function __construct($pdo, $tipo, $descricao){
        $this->pdo = $pdo;
        $this->tipo = $tipo;
        $this->descricao = $descricao;
    }

    public function pegarTipos(){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('SELECT DISTINCT id, tipo FROM tipo_usuario');
            $stmt->execute();
            $tipo_usuarios =  $stmt->fetchAll();

            $log->info('Tipos de usuário listados', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);

            return json_encode($tipo_usuarios);
        } catch (Exception $e) {
            $log->error('Erro ao listar tipos de usuário', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'erro' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }
}