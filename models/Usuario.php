<?php

require_once __DIR__ . '/../utils/config/config.php';

require_once CAMINHO_BASE . '/models/SessionManager.php';
require_once CAMINHO_BASE . '/models/PBI/PowerBISession.php';
require_once CAMINHO_BASE . '/models/Azure/Capacidade.php';
require_once CAMINHO_BASE . '/utils/config/AppLogger.php';
require_once CAMINHO_BASE . '/utils/config/database.php';

class Usuario
{
    private $pdo;
    private $nome;
    private $email;
    private $senha;
    private $tipo;
    private $ativo;
    private const LOG_FILE = 'Usuario';

    public function __construct($pdo, $nome, $email, $senha, $tipo = 'cliente', $ativo = 1)
    {
        // $this->pdo = $pdo;
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
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('
            SELECT 
                u.nome, u.email, tu.id, tu.tipo, u.ativo 
            FROM 
                Usuario u 
            LEFT JOIN 
                TipoUsuario tu 
            ON 
                u.tipo = tu.id 
            ');
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }

            $stmt->close();
            
            $log->info('Usuários listados', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);

            return json_encode($usuarios);
        } catch (Exception $e) {
            $log->error('Erro ao listar usuários', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function excluir(){
        /* Inativa usuários (não exclui, somente coloca um flag no registro dizendo que está inativo) */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('UPDATE Usuario SET ativo = 0 WHERE email = ?');
            $stmt->bind_param('s', $this->email);
            $stmt->execute();
            $stmt->close();

            $log->info('Usuário' . $this->email . 'excluído (inativado) com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {
            $log->error('Exceção ao excluir (inativar) usuário' . $this->email, ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function editar(){
        /* Edita usuários no Banco de Dados */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('UPDATE Usuario SET nome = ?, tipo = ?, ativo = ? WHERE email = ?');
            $stmt->bind_param('siis', $this->nome, $this->tipo, $this->ativo, $this->email);
            $stmt->execute();
            $stmt->close();
            $log->info('Usuário editado com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário editado com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {

            $log->error('Erro ao editar usuário', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function registrar() {
        /* Registra novos usuários a partir de tela de login */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM Usuario WHERE email = ?');
            $stmt->bind_param('s', $this->email);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = [];
                
            while ($row = $result->fetch_assoc()) {
                $usuario[] = $row;
            }
            $stmt->close();

            if ($usuario) {
                $log->info('Usuário com esse email (' . $this->email .') já existe', ['page' => $_SERVER['REQUEST_URI']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário com esse email já existe!']);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO Usuario (nome, email, senha) VALUES (?, ?, ?)');
                $hashedPassword = password_hash($this->senha, PASSWORD_DEFAULT);
                $stmt->bind_param('sss', $this->nome, $this->email, $hashedPassword);
                $stmt->execute();
                $stmt->close();
                
                $log->info('Usuário ' . $this->email .' registrado com sucesso', ['page' => $_SERVER['REQUEST_URI']]);
                return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
            }
        } catch (Exception $e) {
            $log->error('Erro ao registrar usuário', ['error' => $e->getMessage(), 'nome' => $this->nome, 'email' => $this->email, 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['sucesso' => false, 'erro' => 'Não foi possível registrar, tente novamente mais tarde.', 'nome' => $this->nome, 'email' => $this->email]);
        }
    }
    public function registrarComoAdmin() {
        /* Registra novos usuários a partir de tela de cadastro de usuários (necessário estar logado) */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM Usuario WHERE email = ?');

            $stmt->bind_param('s', $this->email);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = [];

            while ($row = $result->fetch_assoc()) {
                $usuario[] = $row;
            }

            $stmt->close();

            if ($usuario) {
                $log->info('Usuário com esse email (' . $this->email .') já existe', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário com esse email já existe!']);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO Usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)');
                $hashedPassword = password_hash($this->senha, PASSWORD_DEFAULT);
                $log->info('Registrando usuário ', ['nome' => $this->nome, 'email' => $this->email, 'tipo' => $this->tipo]);
                $stmt->bind_param('ssss', $this->nome, $this->email, $hashedPassword, $this->tipo);
                $stmt->execute();
                $stmt->close();

                $log->info('Usuário ' . $this->email .' registrado com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
                return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
            }
        } catch (Exception $e) {
            $log->error('Erro ao registrar usuário', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage(), 'tipo' => $this->tipo, 'nome' => $this->nome, 'email' => $this->email, 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['sucesso' => false, 'erro' => 'Erro: ' . $e->getMessage(), 'tipo' => $this->tipo, 'nome' => $this->nome, 'email' => $this->email]);
        }
    }

    public function login()
    {
        /* Realiza login de usuários a partir de tela de login */
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    u.id, u.nome, u.email, u.tipo, u.senha, p.caminho_pagina AS pagina_padrao, u.ativo
                FROM 
                    Usuario u
                LEFT JOIN 
                    TipoUsuario tu
                ON
                    u.tipo = tu.id
                LEFT JOIN
                    Pagina p
                ON
                    tu.pagina_padrao = p.id
                WHERE 
                    u.email = ?
            ');

            $stmt->bind_param('s', $this->email);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = [];

            while ($row = $result->fetch_assoc()) {
                $usuario[] = $row;
            }

            $stmt->close();

            if(count($usuario) > 1){
                $log->error('Erro ao realizar login, mais de um usuário com o mesmo email', ['email' => $this->email]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Erro ao realizar login, mais de um usuário com o mesmo email']);
            }
            if (!$usuario) {    
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
            }

            $usuario = $usuario[0];

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
            $log->error("Exceção ao realizar login", ['error' => $e->getMessage(), 'page' => $_SERVER['REQUEST_URI']]);
            return json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível fazer login, tente novamente mais tarde.']);
        }
        }
        
        public function logout()
        /* Faz o logout, inativndo sessão do PBI e destruindo a sessão */
        {
            $log = AppLogger::getInstance(self::LOG_FILE);
            try {
                $sessao_pbi = new PowerBISession($this->pdo, $_SESSION['id_usuario']);
                $sessao_pbi->inativarSessaoPBI();
    
                SessionManager::sessaoIniciada();
                session_destroy();
    
                return json_encode(['sucesso' => true, 'message' => 'Logout bem-sucedido']);
            } catch (Exception $e) {
                $log->error('Exceção ao realizar logout', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
                return json_encode(['sucesso' => false, 'mensagem' => 'Exceção ao realizar logout' . $e->getMessage()]);
            }
        }
    }