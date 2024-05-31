<?php
class SessionManager{
    public static function sessaoIniciada(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        } 
    }

    public static function iniciarSessao($id_usuario, $nome, $email){
        self::sessaoIniciada();
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['sessao_validade'] = time(); // 1 hora
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
    }
    
    public static function validarCsrfToken() {
        self::sessaoIniciada();
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token Inválido!');
        }
        $_SESSION['csrf_token_validade'] = time();
        $_SESSION['sessao_validade'] = time();
    }
    
    public static function checarValidadeSessao() {
        self::sessaoIniciada();
        if (time() - $_SESSION['sessao_validade'] > 3600) {
            $pdo = (new Database())->getConnection();
            $usuario = new Usuario($pdo, null, null, null);
            $usuario->logout();
            exit;
        }
    }
    public static function checarCsrfToken() {
        self::sessaoIniciada();
        if (empty($_SESSION['csrf_token']) || time() - $_SESSION['csrf_token_validade'] > 3600) {
            $_SESSION['csrf_token_validade'] = time();
    
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    public static function checarSessao() {
        self::sessaoIniciada();
        if (empty($_SESSION['id_usuario'])){
            header('Location: login.php');
            exit;
        }
    }
}


