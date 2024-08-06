<?php
require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\models\\Usuario.php';
require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\SessionManager.php';

$pdo = (new Database())->getConnection();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Determine a ação com base em um campo de formulário oculto
    $action = $_GET['action'];

    if ($action === 'pegarUsuarios') {
        $usuario = new Usuario($pdo, null, null, null);
        $message = $usuario->pegarUsuarios();
        echo $message;
        return;
    }

    if ($action === 'logout') {
        $usuario = new Usuario($pdo, null, null, null);
        $message = $usuario->logout();
        echo $message;
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine a ação com base em um campo de formulário oculto
    $action = $_POST['action'];
    // SessionManager::validarCsrfToken();
    
    if ($action === 'register') {
        SessionManager::validarCsrfToken();
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');
        
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
        $usuario = new Usuario($pdo, $nome, $email, $senha);
        $message = $usuario->register();
        echo $message;
        return;
    }
    if ($action === 'login') {
        SessionManager::validarCsrfToken();
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');

        $usuario = new Usuario($pdo, null, $email, $senha);
        $message = $usuario->login();
        echo $message;
        return;
    } 

    if ($action === 'excluir') {
        SessionManager::validarCsrfToken();
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $usuario = new Usuario($pdo, null, $email, null);
        $message = $usuario->excluir();
        echo $message;
        return;
    }

    if ($action === 'editar') {
        SessionManager::validarCsrfToken();
        $email = htmlspecialchars($_POST['email-editar'], ENT_QUOTES, 'UTF-8');
        $nome = htmlspecialchars($_POST['nome-editar'], ENT_QUOTES, 'UTF-8');
        $tipo = htmlspecialchars($_POST['tipo-editar'], ENT_QUOTES, 'UTF-8');
        $usuario = new Usuario($pdo, $nome, $email, null, $tipo);
        $message = $usuario->editar();
        echo $message;
        return;
    }
}