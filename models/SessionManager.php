<?php

require_once __DIR__ . '/../utils/config/config.php';

require_once CAMINHO_BASE . '/models/Usuario.php';

class SessionManager{
    private const LOG_FILE = 'GerenciadorSessao';

    public static function sessaoIniciada(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        } 
    }

    public static function iniciarSessao($id_usuario, $nome, $email, $tipo_usuario){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            $log->info('Inicando sessão de', ['user' => $id_usuario]);
            self::sessaoIniciada();
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['sessao_validade'] = time(); // 1 hora
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            $log->info('Sessão iniciada', ['user' => $id_usuario]);
            return json_encode(['sucesso' => true, 'mensagem' => 'Sessão iniciada']);
        } catch (Exception $e) {
            $log->error('Erro ao iniciar sessão', ['user' => $id_usuario, 'page' => $_SERVER['REQUEST_URI'],'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }

    }
    
    public static function validarCsrfToken() {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            self::sessaoIniciada();
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $log->error('CSRF token Inválido', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'], 'token' => $_POST['csrf_token'], 'session_token' => $_SESSION['csrf_token']]);
                die('CSRF token Inválido!');
            }
            $_SESSION['sessao_validade'] = time();

            # Não renova o token, pois estaríamos renovando a cada requisição, sendo que o formulário só atualiza o token quando a página é recarregada
            // //TIRAR
            // $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            // //TIRAR

            $log->info('CSRF token validado e renovado até '. time(), ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
        } catch (Exception $e) {
            $log->error('Erro ao validar CSRF token', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'],'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }
    
    public static function destruindoSessao() {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            self::sessaoIniciada();
            $pdo = (new Database())->getConnection();
            $usuario = new Usuario($pdo, null, null, null);
            $usuario->logout();
            $log->info('Sessão destruída', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            header('Location: /views/erro/sessao_expirada.php');
        } catch (Exception $e){
            $log->error('Erro ao checar validade da sessao', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'],'error' => $e->getMessage()]);
        }

    }

    public static function renovarSessao(){
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            self::sessaoIniciada();
            $_SESSION['sessao_validade'] = time();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $log->info('Sessão renovada', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
        } catch (Exception $e){
            $log->error('Erro ao renovar sessao', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'],'error' => $e->getMessage()]);
        }
    }
    public static function checarCsrfToken() {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try {
            $log->info('Checando CSRF token', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
            self::sessaoIniciada();
            if (time() - $_SESSION['sessao_validade'] < 3600) {
                self::renovarSessao();
            } else {
                self::destruindoSessao();
            }
        } catch(Exception $e){
            $log->error('Erro ao checar CSRF token', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI'],'error' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }
    public static function checarSessao() {
        $log = AppLogger::getInstance(self::LOG_FILE);
        try{
            self::sessaoIniciada();
            if (empty($_SESSION['id_usuario'])){
                header('Location: /login.php');
                exit;
            }
            $log->info('Possui sessão, acesso liberado.', ['user' => $_SESSION['id_usuario'], 'page' => $_SERVER['REQUEST_URI']]);
        } catch (Exception $e){
            $log->error('Erro ao verificar se usuário possui sessão', ['page' => $_SERVER['REQUEST_URI'], 'error' => $e->getMessage()]);
        }
    }
}


