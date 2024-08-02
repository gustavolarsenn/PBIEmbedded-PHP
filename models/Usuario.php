<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\SessionManager.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';
require_once CAMINHO_BASE . '\\models\\Azure\\Capacidade.php';

class Usuario
{
    private $pdo;
    private $nome;
    private $email;
    private $senha;

    public function __construct($pdo, $nome, $email, $senha)
    {
        $this->pdo = $pdo;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
    }

    public function pegarUsuarios()
    {
        try {
            // header('Content-Type: application/json');
            $stmt = $this->pdo->prepare('SELECT nome, email, tipo FROM usuario');
            $stmt->execute();
            $usuarios = $stmt->fetchAll();
    
            return json_encode($usuarios);
        } catch (PDOException $e) {
            $error = $e->getMessage();
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $error]);
        }
    }
    public function register()
    {
        // Verifique se o usuário já existe
        $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = ?');
        $stmt->execute([$this->email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuário já existe']);
        } else {
            try {
                // Insira o novo usuário no banco de dados
                $stmt = $this->pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
                $stmt->execute([$this->nome, $this->email, password_hash($this->senha, PASSWORD_DEFAULT)]);
                
                return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
            } catch (PDOException $e) {
                $error = $e->getMessage();
                return json_encode(['sucesso' => false, 'erro' => `Erro:` . $error]);
            }
        }
    }

    public function login()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = ?');
        $stmt->execute([$this->email]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
        }
        // Verifique se o usuário existe e a senha está correta
        if ($this->email && password_verify($this->senha, $usuario['senha'])) {
            SessionManager::iniciarSessao($usuario['id'], $usuario['nome'], $usuario['email']);

            return json_encode(['sucesso' => true, 'mensagem' => 'Login bem-sucedido']);
        } else {
            return json_encode(['sucesso' => false, 'mensagem' => 'Nome de usuário ou senha inválidos']);
            }
        }
        
        public function logout()
        {
            $sessao_pbi = new PowerBISession($this->pdo, $_SESSION['id_usuario']);
            $sessao_pbi->inativarSessaoPBI();

            $capacidade = new Capacidade();
            $capacidade->gatilhoTarefa();

            SessionManager::sessaoIniciada();
            session_destroy();

            return json_encode(['sucesso' => true, 'message' => 'Logout bem-sucedido']);
        }
    }