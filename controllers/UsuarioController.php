<?php
require_once __DIR__ . '\\..\\config\\config.php';

require_once CAMINHO_BASE . '\\models\\Usuario.php';
require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\SessionManager.php';

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
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : null;
    $senha = isset($_POST['senha']) ? htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8') : null;
    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') : null;
    $tipo = isset($_POST['tipo']) ? htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8') : null;
    $status = isset($_POST['status']) ? htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8') : null;
    
    $action = $_POST['action'];
    if ($action === 'login') {
        $usuario = new Usuario($pdo, null, $email, $senha);
        $message = $usuario->login();
        echo $message;
        return;
    } 
    if ($action === 'registrar') {
        try {
            $usuario = new Usuario($pdo, $nome, $email, $senha);
            $message = $usuario->registrar();
            echo $message;
            return;
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'Erro: ' . $e->getMessage()]);
        }
    }

    SessionManager::validarCsrfToken();
    if ($action === 'registrarComoAdmin') {
        try {
            $usuario = new Usuario($pdo, $nome, $email, $senha, $tipo);
            $message = $usuario->registrarComoAdmin();
            echo $message;
            return;
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => 'Erro: ' . $e->getMessage()]);
        }
    }

    if ($action === 'excluir') {
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
        $status = htmlspecialchars($_POST['status-editar'], ENT_QUOTES, 'UTF-8');
        $usuario = new Usuario($pdo, $nome, $email, null, $tipo, $status);
        $message = $usuario->editar();
        echo $message;
        return;
    }
}