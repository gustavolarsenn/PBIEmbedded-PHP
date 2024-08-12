<?php

require_once __DIR__ . '\\..\\config.php'; 

require_once CAMINHO_BASE . '\\SessionManager.php';
require_once CAMINHO_BASE . '\\models\\PermissoesPagina.php';
require_once CAMINHO_BASE . '\\config\\database.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

$pdo = (new Database())->getConnection();
$permissaoPagina = new PermissoesPagina($pdo, basename(__FILE__, ".php"), $_SESSION['tipo_usuario'], null, $_SESSION['id_usuario']);
$paginasPermitidas = $permissaoPagina->paginasPermitidas();

$categorias = $permissaoPagina->pegarCategorias();

// Pega lista de categorias permitidas para o usuário
$categoriasPermitidas = array_map(function($pagina) {
    return $pagina['categoria'];
}, $paginasPermitidas);

// Filtra somente as categorias permitidas
$categorias = array_filter($categorias, function($categoria) use ($categoriasPermitidas) {
    return in_array($categoria['categoria'], $categoriasPermitidas);
});
?>

<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label first">Módulo Operacional</li>
            <?php foreach ($categorias as $categoria): ?>
                <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="<?php echo $categoria['icon']?>"></i>
                        <span class="nav-text"><?php echo $categoria['categoria']?></span>
                    </a>
                    <ul aria-expanded="false">
                        <?php foreach ($paginasPermitidas as $pagina): ?>
                            <ul aria-expanded="false"  <?php if($categoria['categoria'] === 'Relatórios - BI'): ?> id="bi-reports" <?php continue; endif; ?>>
                            <?php if ($pagina['categoria'] === $categoria['categoria']): ?>
                                <li>
                                    <a href="<?php echo '/' . $pagina['caminho_pagina']; ?>">
                                        <?php echo $pagina['pagina']; ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        <?php endforeach; ?>
                    </ul>
                    <?php endforeach; ?>

        </ul>
    </div>
</div>