<?php

require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\models\\SessionManager.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';
require_once CAMINHO_BASE . '\\models\\Azure\\Capacidade.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Usuario
{
    private $pdo;
    private $nome;
    private $email;
    private $senha;
    private $tipo;
    private $ativo;
    private const LOG_FILE = 'Usuario';
    private const LOG = 'usuario';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';

    public function __construct($pdo, $nome, $email, $senha, $tipo = 'cliente', $ativo = 1)
    {
        $this->pdo = $pdo;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->tipo = $tipo;
        $this->ativo = $ativo;
    }

    public function pegarUsuarios()
    {
        /* Busca listagem de todos os usuários*/
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                u.nome, u.email, tu.id, tu.tipo, u.ativo 
            FROM 
                usuario u 
            LEFT JOIN 
                tipo_usuario tu 
            ON 
                u.tipo = tu.id 
            ');
            $stmt->execute();
            $usuarios = $stmt->fetchAll();
    
            $log->info('Usuários listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);

            return json_encode($usuarios);
        } catch (Exception $e) {
            $log->error('Erro ao listar usuários', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function excluir(){
        /* Inativa usuários (não exclui, somente coloca um flag no registro dizendo que está inativo) */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('UPDATE usuario SET ativo = 0 WHERE email = ?');
            $stmt->execute([$this->email]);
    
            $log->info('Usuário' . $this->email . 'excluído (inativado) com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {
            $log->error('Exceção ao excluir (inativar) usuário' . $this->email, ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function editar(){
        /* Edita usuários no Banco de Dados */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('UPDATE usuario SET nome = ?, tipo = ?, ativo = ? WHERE email = ?');
            $stmt->execute([$this->nome, $this->tipo, $this->ativo, $this->email]);
    
            $log->info('Usuário editado com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário editado com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {

            $log->error('Erro ao editar usuário', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function registrar() {
        /* Registra novos usuários a partir de tela de login */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = ?');
            $stmt->execute([$this->email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $log->info('Usuário com esse email (' . $this->email .') já existe', ['page' => $_SERVER['HTTP_REFERER']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário com esse email já existe!']);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
                $stmt->execute([$this->nome, $this->email, password_hash($this->senha, PASSWORD_DEFAULT)]);
                
                $log->info('Usuário ' . $this->email .' registrado com sucesso', ['page' => $_SERVER['HTTP_REFERER']]);
                return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
            }
        } catch (Exception $e) {
            $log->error('Erro ao registrar usuário', ['error' => $e->getMessage(), 'nome' => $this->nome, 'email' => $this->email, 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['sucesso' => false, 'erro' => 'Não foi possível registrar, tente novamente mais tarde.', 'nome' => $this->nome, 'email' => $this->email]);
        }
    }
    public function registrarComoAdmin() {
        /* Registra novos usuários a partir de tela de cadastro de usuários (necessário estar logado) */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = ?');
            $stmt->execute([$this->email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $log->info('Usuário com esse email (' . $this->email .') já existe', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário com esse email já existe!']);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)');
                $stmt->execute([$this->nome, $this->email, password_hash($this->senha, PASSWORD_DEFAULT), $this->tipo]);
                
                $log->info('Usuário ' . $this->email .' registrado com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
                return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
            }
        } catch (Exception $e) {
            $log->error('Erro ao registrar usuário', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage(), 'tipo' => $this->tipo, 'nome' => $this->nome, 'email' => $this->email, 'page' => $_SERVER['HTTP_REFERER']]);
            return json_encode(['sucesso' => false, 'erro' => 'Erro: ' . $e->getMessage(), 'tipo' => $this->tipo, 'nome' => $this->nome, 'email' => $this->email]);
        }
    }

    public function login()
    {
        /* Realiza login de usuários a partir de tela de login */
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            throw new Exception('Erro ao realizar login');
            $stmt = $this->pdo->prepare('
                SELECT 
                    u.id, u.nome, u.email, u.tipo, u.senha, p.caminho_pagina AS pagina_padrao, u.ativo
                FROM 
                    usuario u
                LEFT JOIN 
                    tipo_usuario tu
                ON
                    u.tipo = tu.id
                LEFT JOIN
                    pagina p
                ON
                    tu.pagina_padrao = p.id
                WHERE 
                    u.email = ?
            ');
            $stmt->execute([$this->email]);
            $usuario = $stmt->fetch();
            
            if (!$usuario) {
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
            }

            if (!$usuario['ativo']) {
                $log->info("Tentativa de login com usuário inativo!", ['user' => $usuario['id']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário inativo']);
            }
            // Verifique se o usuário existe e a senha está correta
            if ($this->email && password_verify($this->senha, $usuario['senha'])) {
                SessionManager::iniciarSessao($usuario['id'], $usuario['nome'], $usuario['email'], $usuario['tipo']);
    
                $log->info('Login realizado com sucesso', ['user' => $usuario['id']]);
                return json_encode(['sucesso' => true, 'mensagem' => 'Login bem-sucedido', 'pagina_padrao' => $usuario['pagina_padrao']]);
            } else {
                $log->info("Tentativa de login sem sucesso, usuário ou senha inválidos!", ['user' => $usuario['id']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Nome de usuário ou senha inválidos']);
            }
        } catch (Exception $e) {
            $log->error("Exceção ao realizar login", ['error' => $e->getMessage(), 'page' => $_SERVER['HTTP_REFERER']]);
            error_log("Oh no! We are out of FOOs!", 1, "gustavo.larsen@zport.com.br");
            return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível fazer login, tente novamente mais tarde.']);
        }
        }
        
        public function logout()
        /* Faz o logout, inativndo sessão do PBI e destruindo a sessão */
        {
            $log = new Logger(self::LOG);
            $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
            try {
                $sessao_pbi = new PowerBISession($this->pdo, $_SESSION['id_usuario']);
                $sessao_pbi->inativarSessaoPBI();
    
                SessionManager::sessaoIniciada();
                session_destroy();
    
                return json_encode(['sucesso' => true, 'message' => 'Logout bem-sucedido']);
            } catch (Exception $e) {
                $log->error('Exceção ao realizar logout', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['HTTP_REFERER']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Exceção ao realizar logout' . $e->getMessage()]);
            }
        }
    }