<?php

$basePath = '../../';

require_once __DIR__ . '\\..\\..\\config.php'; 

require_once CAMINHO_BASE . '\\SessionManager.php';
require_once CAMINHO_BASE . '\\models\\PermissoesPagina.php';
require_once CAMINHO_BASE . '\\config\\database.php';

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
        <title>Controle de Prancha</title>

        <link rel="icon" type="image/png" href="/img/icone.png">

        <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.carousel.min.css">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.theme.default.min.css">
        <link href="<?php echo $basePath; ?>/vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
        <link href="<?php echo $basePath; ?>/css/style.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/css/charts.css">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/css/prancha.css">

        <link href="<?php echo $basePath; ?>/css/MultiSelect.css" rel="stylesheet" type="text/css">
    </head>
    <style>

    </style>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
    <script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
    <script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
        
        <?php include_once $basePath . '/components/loader.php'?>

        <div id="main-wrapper">

            <?php include_once $basePath . '/components/header.php'?>
            
            <?php include_once $basePath . '/components/sidebar.php'?>

            <div class="content-body">
                <div class="container-fluid">

                    <div class="filter-container">
                        <div class="input-label">
                            <label>Navio</label>
                            <select id='lista-navio' data-multi-select>
                            </select>
                        </div>
                        <div class="input-label">
                            <label>Relatório nº</label>
                            <select id="lista-relatorio_no" multiple data-multi-select>
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
                        <div id='clean-filters' class="input-label" style="width: 5%" title="Limpar filtros" onclick="cleanFiltersField(['navio', 'periodo', 'relatorio_no', 'motivo_paralisacao']); cleanFiltersData();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16" style="margin: auto 10px;">
                                <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                            </svg>
                        </div>
                    </div>
                    <section>

                            <div class="chart-container">
                                <div id="info-navio-container" class="chart chart-small-block">
                                    <h1 id="info-navio-titulo"></h1>
                                    <div id="info-navio">
                                        <div class="info-navio-row">
                                            <h4>Berço: </h4>
                                            <label id="info-berth"></label>
                                        </div>
                                        <div class="info-navio-row">
                                            <h4>Produto: </h4>
                                            <label id="info-product"></label>
                                        </div>
                                        <div class="info-navio-row">
                                            <h4>Modalidade: </h4>
                                            <label id="info-modality"></label>
                                        </div>
                                        <div class="info-navio-row">
                                            <h4>Manifestado: </h4>
                                            <label id="info-volume"></label>
                                        </div>
                                        <div class="info-navio-row">
                                            <h4>Data: </h4>
                                            <label id="info-date"></label>
                                        </div>
                                        <div class="info-navio-row" style="border: none !important">
                                            <h4>Prancha mínima: </h4>
                                            <label id="info-minimum-discharge"></label>
                                        </div>
                                    </div>
                                </div>     

                                <div id="descarregado-total-dia-prancha-aferida-container">
                                    <div id="descarregado-total-prancha-aferida-container">
                                        <div id="descarregado-total-container" class="chart chart-small-block">
                                            <label class="label-chart">Total descarregado / restante</label>
                                            <div id="descarregado-total">
                                                    <div id="descarregado-total-grafico-container">
                                                        <canvas id="graficoTotalDescarregado" height="27" width="40"></canvas>
                                                        <div id="emptyGraficoTotalDescarregado" class="no-data">
                                                            <p>Nenhum valor encontrado!</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="vessel-discharging-info">
                                                            <h4>Descarregado: </h4>
                                                            <label id="info-descarregado"></label>
                                                        </div>
                                                        <div class="vessel-discharging-info">
                                                            <h4>Restante: </h4>
                                                            <label id="info-restante"></label>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>  

                                        <div id="prancha-aferida-container" class="chart chart-small-block">
                                                <div id="prancha-aferida-info">
                                                    <div id="prancha-aferida-big-numbers">
                                                            <div>
                                                                <h4>Prancha Aferida</h4>
                                                                <label id="prancha-aferida" class="big-numbers"></label>
                                                            </div>
                                                    </div>
                                                    <div id="meta-alcancada" class="target-stripe">
                                                    </div>
                                                </div>

                                                <div id="prancha-aferida-lista-paralisacao-container">
                                                    <div class="input-label">
                                                        <select id='lista-motivo_paralisacao' data-multi-select>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <ul id="paralisacao-selecionada">
                                                        </ul>                                                
                                                    </div>
                                                </div>
                                            </div>
                                    </div> 
                                    <div id="descarregado-dia-container">
                                        <div id="descarregado-dia-grafico" class="chart chart-small-block">
                                            <label class="label-chart">Descarregado por dia</label>
                                            <canvas id="graficoDescarregadoDia" height="11" width="65"></canvas>
                                            <canvas id="graficoDescarregadoDiaSideBar" height="14" width="65"></canvas>
                                            <div id="emptyGraficoDescarregadoDia" class="no-data">
                                                <p>Nenhum valor encontrado!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div id="resumo-geral-tempo-paralisado-container">
                                    <div id="resumo-geral-grafico" class="chart chart-small-block">
                                        <label class="label-chart">Resumo geral</label>
                                        <canvas id="graficoResumoGeral" height="45" width="100"></canvas>
                                        <div id="emptyGraficoResumoGeral" class="no-data">
                                            <p>Nenhum valor encontrado!</p>
                                        </div>
                                    </div>
                                    <div class="chart chart-small-block" id="tempo-paralisado-grafico">
                                        <label class="label-chart">Tempo paralisado</label>
                                        <canvas id="graficoTempoParalisado" height="30" width="100"></canvas>
                                        <div id="emptyGraficoTempoParalisado" class="no-data">
                                            <p>Nenhum valor encontrado!</p>
                                        </div>
                                    </div>
                            </div>
                            <div class="chart" style="margin-bottom: 30%">
                                <label class="label-chart">Total descarregado por dia e período, MT</label>
                                <div id="descarregado-dia-periodo-container" class="chart">
                                    <div id='descarregado-dia-periodo-grafico' class="chart chart-small-block">
                                            <canvas id="graficoDescarregadoDiaPeriodo" height="20" width='100'></canvas>
                                            <div id="emptyGraficoDescarregadoDiaPeriodo" class="no-data">
                                                <p>Nenhum valor encontrado!</p>
                                            </div>
                                        </div>
                                </div>
                            </div>
                    </section>
        </div>


    <script src="<?php echo $basePath; ?>/js/relatorios/MultiSelect.js"></script>
    <script type="module" src="<?php echo $basePath; ?>/js/relatorios/prancha/prancha_charts.js"></script>
    <script src="<?php echo $basePath; ?>/js/relatorios/charts_functions.js"></script>

        <!-- Required vendors -->
        <script src="<?php echo $basePath; ?>/vendor/global/global.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/quixnav-init.js"></script>
        <script src="<?php echo $basePath; ?>/js/custom.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/logout.js"></script>
        <script src="<?php echo $basePath; ?>/js/pbi/links_pbi.js"></script>
        
        <script src="<?php echo $basePath; ?>/vendor/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="<?php echo $basePath; ?>/vendor/jquery-validation/jquery.validate.min.js"></script>
        
        <!-- Form validate init -->
        <script src="<?php echo $basePath; ?>/js/plugins-init/jquery.validate-init.js"></script>

        <!-- Chart ChartJS plugin files -->
        <script src="<?php echo $basePath; ?>/vendor/chart.js/Chart.bundle.min.js"></script>
        <script src="<?php echo $basePath; ?>/js/plugins-init/chartjs-init.js"></script>

        <!-- Form step init -->
        <script src="<?php echo $basePath; ?>/js/plugins-init/jquery-steps-init.js"></script>

        <script src="<?php echo $basePath; ?>/js/relatorios/MultiSelect.js"></script>

        </body>
    </html>
    <?php
}