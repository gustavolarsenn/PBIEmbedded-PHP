<?php
require_once '../models/Usuario.php';
require_once '../config/database.php';
require_once '../SessionManager.php';

$pdo = (new Database())->getConnection();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SessionManager::validarCsrfToken();
    
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');
    
    // Determine a ação com base em um campo de formulário oculto
    $action = $_POST['action'];
    if ($action === 'register') {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
        $usuario = new Usuario($pdo, $nome, $email, $senha);
        $message = $usuario->register();

    } else if ($action === 'login') {
        $usuario = new Usuario($pdo, null, $email, $senha);
        $message = $usuario->login();
    }

    if ($message) {
        echo $message;
    }
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];

    if ($action === 'logout') {
        $usuario = new Usuario($pdo, null, null, null);
        $message = $usuario->logout();
        echo $message;
    }
}