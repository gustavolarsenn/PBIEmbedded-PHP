<?php

require_once '../SessionManager.php';

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
            SessionManager::sessaoIniciada();
            session_destroy();

            return json_encode(['sucesso' => true, 'message' => 'Logout bem-sucedido']);
        }
    }