<?php

require_once __DIR__ . '/../config/config.php';

class PermissoesPagina {
    private $pdo;
    private $titulo;
    private $id_tipo_usuario;
    private $id_pagina;
    private $id_usuario;
    private const LOG_FILE = 'PermissoesPagina';
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
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                p.pagina 
            FROM 
                PermissoesPagina pp
            LEFT JOIN 
                Pagina p
            ON 
                pp.id_pagina = p.id
            WHERE 
                pp.id_tipo_usuario = ? 
            AND 
                p.pagina_clean = ?
            AND 
                pp.ativo = 1
            ');
            $stmt->execute([$this->id_tipo_usuario, $this->titulo]);
            $permissoes = $stmt->fetchAll();
            if (count($permissoes) > 0) {
                $log->info('Permissão concedida na página ' . $this->titulo, ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
                return true;
            } else {
                $log->info('Permissão não concedida na página ' . $this->titulo, ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
                return false;
            }
        } catch (Exception $e) {
            $log->error('Erro ao verificar permissão na página ' . $this->titulo, ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function paginasPermitidas()
    {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                cp.categoria,
                p.pagina,
                p.caminho_pagina
            FROM 
                PermissoesPagina pp
            LEFT JOIN
                CategoriasPagina cp
            ON
                pp.id_categoria = cp.id
            LEFT JOIN 
                Pagina p
            ON
                pp.id_pagina = p.id
            LEFT JOIN 
                Usuario u 
            ON 
                pp.id_tipo_usuario = u.tipo 
            WHERE 
                u.id = ?
            AND
                pp.ativo = 1;
            ');
            $stmt->execute([$this->id_usuario]);
            $permissoes = $stmt->fetchAll();
            $log->info('Busca por páginas permitadas realizada com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return $permissoes;
        } catch (Exception $e) {
            $log->error('Erro ao verificar permissões', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return [];
        }
    }

    public function pegarCategorias(){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                DISTINCT categoria, icon
            FROM 
                CategoriasPagina
            ');
            $stmt->execute();
            $categorias = $stmt->fetchAll();
            $log->info('Busca por categorias realizada com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return $categorias;
        } catch (Exception $e) {
            $log->error('Erro ao buscar categorias', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return [];
        }
    }
}