<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\SessionManager.php';
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
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            // header('Content-Type: application/json');
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
    
            $log->info('Usuários listados', ['user' => $_SESSION['id_usuario']]);

            return json_encode($usuarios);
        } catch (Exception $e) {
            $log->error('Erro ao listar usuários', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function excluir(){
        try {
            $stmt = $this->pdo->prepare('UPDATE usuario SET ativo = 0 WHERE email = ?');
            $stmt->execute([$this->email]);
    
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {
            $error = $e->getMessage();
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $error]);
        }
    }

    public function editar(){
        /* Edita usuários no Banco de Dados */
        
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
        try {
            $stmt = $this->pdo->prepare('UPDATE usuario SET nome = ?, tipo = ? WHERE email = ?');
            $stmt->execute([$this->nome, $this->tipo, $this->email]);
    
            $log->info('Usuário editado com sucesso', ['user' => $_SESSION['id_usuario']]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuário editado com sucesso', 'email' => $this->email]);
        } catch (Exception $e) {

            $log->error('Erro ao editar usuário', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    public function register() {
        /* Registra novos usuários a partir de tela de login */

        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = ?');
            $stmt->execute([$this->email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $log->info('Usuário com esse email (' . $this->email .') já existe');
                return json_encode(['sucesso' => false, 'mensagem' => 'Usuário com esse email já existe!']);
            } else {
                    $stmt = $this->pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
                    $stmt->execute([$this->nome, $this->email, password_hash($this->senha, PASSWORD_DEFAULT)]);
                    
                    $log->info('Usuário ' . $this->email .' registrado com sucesso');
                    return json_encode(['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso']);
                }
        } catch (Exception $e) {
            $log->error('Erro ao registrar usuário', ['user' => $_SESSION['id_usuario'], 'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => 'Erro:' . $e->getMessage()]);
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