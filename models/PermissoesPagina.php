<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class PermissoesPagina {
    private $pdo;
    private $titulo;
    private $id_tipo_usuario;
    private $id_pagina;
    private $id_usuario;
    private const LOG_FILE = 'PermissoesPagina';
    private const LOG = 'permissoes_pagina';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';

    public function __construct($pdo, $titulo, $id_tipo_usuario, $id_pagina, $id_usuario)
    {
        $this->pdo = $pdo;
        $this->titulo = $titulo;
        $this->id_tipo_usuario = $id_tipo_usuario;
        $this->id_pagina = $id_pagina;
        $this->id_usuario = $id_usuario;
    }

    public function verificarPermissao()
    {
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                pagina 
            FROM 
                permissoes_pagina
            WHERE 
                id_tipo_usuario = ? 
            AND 
                pagina_clean = ?
            AND 
                ativo = 1
            ');
            $stmt->execute([$this->id_tipo_usuario, $this->titulo]);
            $permissoes = $stmt->fetchAll();
            if (count($permissoes) > 0) {
                $log->info('Permissão concedida na página ' . $this->titulo, ['user' => $_SESSION['id_usuario']]);
                return true;
            } else {
                $log->info('Permissão não concedida na página ' . $this->titulo, ['user' => $_SESSION['id_usuario']]);
                return false;
            }
        } catch (Exception $e) {
            $log->error('Erro ao verificar permissão na página ' . $this->titulo, ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function paginasPermitidas()
    {
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                cp.categoria,
                pp.pagina,
                pp.caminho_pagina
            FROM 
                permissoes_pagina pp
            LEFT JOIN
                categorias_pagina cp
            ON
                pp.id_categoria = cp.id
            LEFT JOIN 
                usuario u 
            ON 
                pp.id_tipo_usuario = u.tipo 
            WHERE 
                u.id = ?
            AND
                pp.ativo = 1;
            ');
            $stmt->execute([$this->id_usuario]);
            $permissoes = $stmt->fetchAll();
            $log->info('Busca por páginas permitadas realizada com sucesso', ['user' => $_SESSION['id_usuario']]);
            return $permissoes;
        } catch (Exception $e) {
            $log->error('Erro ao verificar permissões', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return [];
        }
    }

    public function pegarCategorias(){
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                DISTINCT categoria, icon
            FROM 
                categorias_pagina
            ');
            $stmt->execute();
            $categorias = $stmt->fetchAll();
            $log->info('Busca por categorias realizada com sucesso', ['user' => $_SESSION['id_usuario']]);
            return $categorias;
        } catch (Exception $e) {
            $log->error('Erro ao buscar categorias', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return [];
        }
    }
}