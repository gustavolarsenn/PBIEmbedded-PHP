<?php
require_once __DIR__ . '/../utils/config/config.php';

require_once CAMINHO_BASE . '/models/TipoUsuario.php';
require_once CAMINHO_BASE . '/utils/config/database.php';
require_once CAMINHO_BASE . '/models/SessionManager.php';

$pdo = (new Database())->getConnection();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Determine a ação com base em um campo de formulário oculto
    $action = $_GET['action'];

    if ($action === 'pegarTiposUsuario') {
        $tipoUsuario = new TipoUsuario($pdo, null, null);
        $message = $tipoUsuario->pegarTipos();
        echo $message;
        return;
    }
}