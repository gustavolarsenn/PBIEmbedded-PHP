<?php

$basePath = '../../'; // Adjust this path as needed 

require_once __DIR__ . '/../../config/config.php'; 

require_once CAMINHO_BASE . '/models/SessionManager.php';
require_once CAMINHO_BASE . '/models/PermissoesPagina.php';
require_once CAMINHO_BASE . '/config/database.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

$pdo = (new Database())->getConnection();
$permissaoPagina = new PermissoesPagina($pdo, basename(__FILE__, ".php"), $_SESSION['tipo_usuario'], null, $_SESSION['id_usuario']);
$possuiPermissao = $permissaoPagina->verificarPermissao();

if ($possuiPermissao) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Zport</title>

        <link rel="icon" type="image/png" href="/img/icone.png">

        <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.carousel.min.css">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.theme.default.min.css">
        <link href="<?php echo $basePath; ?>/vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
        <link href="<?php echo $basePath; ?>/css/style.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/css/charts.css">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/css/balanca.css">

        <link href="<?php echo $basePath; ?>/css/MultiSelect.css" rel="stylesheet" type="text/css">
    </head>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
    <script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
    <script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>

        <?php include_once CAMINHO_BASE . '/components/loader.php'; ?>

        <div id="main-wrapper">

            <?php include_once CAMINHO_BASE . '/components/header.php'; ?>
            
            <?php include_once CAMINHO_BASE . '/components/sidebar.php'; ?>

            <div class="content-body">
                <div class="container-fluid">
                    <div class="title-container">
                        <h2>Relatório de balança (prévia)</h2>
                    </div>	
                    <div class="title-container">
                        <div>
                            <h1 id="nome-navio"></h1>
                        </div>
                        <div class="filter-container">
                            <div class="input-label">
                                <label>Navio</label>
                                <select id='lista-navio' data-multi-select>
                                </select>
                            </div>
                            <div class="input-label" >
                                <label>Data</label>
                                <input type="date" id='data'>
                            </div>
                            <div class="input-label">
                                <label>Período</label>
                                <select id='lista-periodo' multiple data-multi-select>
                                </select>
                            </div>
                            <div class="input-label">
                                <label>Porão</label>
                                <select id="lista-porao" multiple data-multi-select>
                                </select>
                            </div>
                            <div class="input-label">
                                <label>Cliente</label>
                                <select id="lista-cliente" multiple data-multi-select>
                                </select>
                            </div>
                            <div class="input-label">
                                <label>Armazém</label>
                                <select id="lista-armazem" multiple data-multi-select>
                                </select>
                            </div>
                            <div class="input-label">
                                <label>Produto</label>
                                <select id="lista-produto" multiple data-multi-select>
                                </select>
                            </div>
                            <div class="input-label">
                                <label>DI</label>
                                <select id="lista-di"  data-placeholder="Selecione DIs" multiple data-multi-select>
                                </select>
                            </div>
                            <div id='clean-filters' class="input-label" style="width: 5%" title="Limpar filtros" onclick="cleanFiltersField(['navio', 'periodo', 'porao', 'cliente', 'armazem', 'produto', 'di']); cleanFiltersData();">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16" style="margin: auto 10px;">
                                    <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <section>
                            <div id='descarregado-total-porao-cliente-di-container'>
                                <div id='descarregado-total-porao-container'>
                                    <div id='descarregado-total-grafico' class="chart chart-small-block">
                                        <label class="label-chart">Total descarregado / Restante</label>
                                        <canvas id="graficoDescarregadoResto" height="25" width="50"></canvas>
                                        <div id="emptyGraficoDescarregadoResto" class="no-data">
                                            <p>Nenhum valor encontrado!</p>
                                        </div>
                                    </div>
                                    <div id='descarregado-porao-grafico' class="chart chart-small-block">
                                        <label class="label-chart">Descarregamento por porão</label>
                                        <canvas id="graficoRealizadoPorao" height="25" width="50"></canvas>
                                        <div id="emptyGraficoRealizadoPorao" class="no-data">
                                            <p>Nenhum valor encontrado!</p>
                                        </div>
                                    </div>
                                </div>
                                    <div id="volume-cliente-di-grafico" class="chart">
                                        <label class="label-chart">Volume por cliente e DI</label>
                                        <canvas id="graficoRealizadoClienteDI" height="27" width="55"></canvas>
                                        <div id="emptyGraficoRealizadoClienteDI" class="no-data">
                                            <p>Nenhum valor encontrado!</p>
                                        </div>
                                    </div>
                            </div>
                            <div id="descarregado-dia-volume-cliente-container">
                                <div id="descarregado-dia-grafico" class="chart half-chart">
                                    <label class="label-chart">Descarregado por dia</label>
                                    <canvas id="graficoVolumeDia" height="25" width="50"></canvas>
                                    <div id="emptyGraficoVolumeDia" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                                <div id='volume-cliente-grafico' class="chart half-chart">
                                    <label class="label-chart">Volume por cliente</label>
                                    <canvas id="graficoVolumeCliente" height="25" width="50"></canvas>
                                    <div id="emptyGraficoVolumeCliente" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                            </div>
                                <div id='descarregado-dia-periodo-grafico'class="chart full-chart">
                                    <label class="label-chart">Descarregamento por dia e período</label>
                                    <canvas id="graficoVolumeDiaPeriodo" height="11" width="50"></canvas>
                                    <div id="emptyGraficoVolumeDiaPeriodo" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                    </section>
                </div>
            <?php include_once CAMINHO_BASE . '/components/footer.php'?>

        <!-- Filtros -->
        <script src="<?php echo $basePath; ?>/js/relatorios/MultiSelect.js"></script>
        
        <!-- Geração de gráficos -->
        <script type="module" src="<?php echo $basePath; ?>/js/relatorios/balanca/balanca_charts.js"></script>
        <script src="<?php echo $basePath; ?>/js/relatorios/charts_functions.js"></script>
        
        <!-- Gerar links para relatórios PBI -->
        <script src="<?php echo $basePath; ?>/js/pbi/links_pbi.js"></script>

        <!-- Chart ChartJS plugin files -->
        <script src="<?php echo $basePath; ?>/vendor/chart.js/Chart.bundle.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/plugins-init/chartjs-init.js"></script>

        <!-- Required vendors -->
        <script src="<?php echo $basePath; ?>/vendor/global/global.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/quixnav-init.js"></script>
        <script src="<?php echo $basePath; ?>/js/custom.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/logout.js"></script>
        
        <script src="<?php echo $basePath; ?>/vendor/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="<?php echo $basePath; ?>/vendor/jquery-validation/jquery.validate.min.js"></script>
        
        <!-- Form validate init -->
        <script src="<?php echo $basePath; ?>/js/plugins-init/jquery.validate-init.js"></script>

        <!-- Form step init -->
        <script src="<?php echo $basePath; ?>/js/plugins-init/jquery-steps-init.js"></script>

        </body>
    </html>
    <?php
} else {
    include_once CAMINHO_BASE . '/components/pagina_desconhecida.php';
}