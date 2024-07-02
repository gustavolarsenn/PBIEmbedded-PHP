<?php

$basePath = '..'; // Adjust this path as needed 

require $basePath . '/SessionManager.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo $embedInfo;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Controle de Prancha</title>

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

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">


        <div class="nav-header">
            <a href="index.php" class="brand-logo">
                <img class="logo-compact" src="<?php echo $basePath; ?>/images/logo-zport-branca-3x.png" alt="">
                <img class="brand-title" src="<?php echo $basePath; ?>/images/logo-zport-branca-3x.png" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="search_bar dropdown">
                                <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <div class="dropdown-menu p-0 m-0">
                                    <form>
                                        <input class="form-control" type="search" placeholder="Pesquisar" aria-label="Search">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-bell"></i>
                                    <div class="pulse-css"></div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="list-unstyled">
                                        <li class="media dropdown-item">
                                            
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-account"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="./app-profile.html" class="dropdown-item">
                                        <i class="icon-user"></i>
                                        <span class="ml-2">Profile </span>
                                    </a>
                                    <a href="./email-inbox.html" class="dropdown-item">
                                        <i class="icon-envelope-open"></i>
                                        <span class="ml-2">Inbox </span>
                                    </a>
									<a class="dropdown-item" href="#" onclick="logoutConfirmation()" >
										<i class="icon-key"></i>
										<span class="ml-2">Logout</span>
									</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Módulo Operacional</li>
					
					<li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i class="icon icon-single-04"></i><span class="nav-text">Cadastro</span></a>
                        <ul aria-expanded="false">
                        <li><a href="<?php echo $basePath; ?>/cadNavios.php">Navios</a></li>	
						<li><a href="<?php echo $basePath; ?>/cliente.php">Clientes</a></li>
						<li><a href="<?php echo $basePath; ?>/carga.php">Carga</a></li>
                        </ul>
                    </li>
					
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i class="icon icon-form"></i><span class="nav-text">Inclusão</span></a>
                        <ul aria-expanded="false">
                        <li><a href="<?php echo $basePath; ?>/Escala1.php">Escala</a></li>
						<li><a href="<?php echo $basePath; ?>/paralizacao.php">Paralizações</a></li>
						<li><a href="<?php echo $basePath; ?>/periodosTrabalhados.php">Periodo Trabalhado</a></li>
						<li><a href="<?php echo $basePath; ?>/planoDistribuicao.php">Plano de Distribuição</a></li>
						
                        </ul>
                    </li>
				
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                                class="icon icon-layout-25"></i><span class="nav-text">Relatórios</span></a>
                        <ul aria-expanded="false">
                        <li><a href="<?php echo $basePath; ?>/relatorioEscala1.php">Relatório de Escala</a></li>	
						<li><a href="<?php echo $basePath; ?>/solRelatorioDescarga1.php">Relatório por periodo</a></li>
						<li><a href="<?php echo $basePath; ?>/solRelatorioCliente.php">Relatório por cliente</a></li>
						<li><a href="<?php echo $basePath; ?>/cadChuvaNavio.php">Relatório Chuva</a></li>
						<li><a href="<?php echo $basePath; ?>/relatorios/relatorio_balanca.php">Relatório Balança</a></li>
						<li><a href="<?php echo $basePath; ?>/relatorios/relatorio_controle_prancha.php">Controle de Prancha</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                                class="icon icon-layout-25"></i><span class="nav-text">Relatórios - BI</span></a>
                        <ul aria-expanded="false" id="bi-reports">
                        </ul>
                    </li>
                </ul>
            </div>
        </div>


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
                    <div id='clean-filters' class="input-label" style="width: 5%" title="Limpar filtros" onclick="cleanFiltersField(['navio', 'periodo', 'relatorio_no']); cleanFiltersData();">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16" style="margin: auto 10px;">
                            <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                        </svg>
                    </div>
                </div>
                <section>

                        <div style="min-width: 100%; display: flex; height: 100%; margin: 0px 0px 10px 0px;">
                                <div class="chart chart-small-block" style="width: 60%; height: 30vh;">
                                    <label class="label-chart"></label>
                                    <h1 id="info-vessel" style="text-align: center"></h1>
                                  
                                    <div style="display: flex; justify-content: space-evenly;">
                                        <div style="display: block; justify-content: space-evenly; width: 50%;">
                                            <div class="vessel-info">
                                                <h4>Berço: </h4>
                                                <label id="info-berth"></label>
                                            </div>
                                            <div class="vessel-info">
                                                <h4>Produto: </h4>
                                                <label id="info-product"></label>
                                            </div>
                                            <div class="vessel-info" style="border: none !important">
                                                <h4>Modalidade: </h4>
                                                <label id="info-modality"></label>
                                            </div>
                                        </div>
                                        <div style="display: block; justify-content: space-evenly; width: 50%;">
                                            <div class="vessel-info">
                                                <h4>Manifestado: </h4>
                                                <label id="info-volume"></label>
                                            </div>
                                            <div class="vessel-info">
                                                <h4>Data: </h4>
                                                <label id="info-date"></label>
                                            </div>
                                            <div class="vessel-info" style="border: none !important">
                                                <h4>Prancha mínima: </h4>
                                                <label id="info-minimum-discharge"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <canvas id="graficoInfos" class='any-chart' ></canvas> -->

                                </div>       
                                <div class="chart chart-small-block" style="width: 40%; height: 30vh;  margin: 0 0 0 10px;">
                                    <label class="label-chart">Prancha aferida</label>
                                    <!-- <canvas id="graficoPranchaAferida" class='any-chart'></canvas> -->
                                    <div id="emptyGraficoPranchaAferida" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div> 
                        </div>
                        <div style="min-width: 100%; display: flex; height: 100%; margin: 0px 0px 10px 0px;">
                                <div class="chart chart-small-block" style="width: 40%; height: 30vh;">
                                    <label class="label-chart">Total descarregado / restante</label>
                                    <canvas id="graficoTotalDescarregado" height="18" width="50"></canvas>
                                    <div id="emptyGraficoTotalDescarregado" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                                <div class="chart chart-small-block" style="width: 60%; margin: 0 0 0 10px; height: 30vh;">
                                    <label class="label-chart">Descarregado por dia</label>
                                    <canvas id="graficoDescarregadoDia" height="15" width="60"></canvas>
                                    <div id="emptyGraficoDescarregadoDia" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                        </div>
                        <div style="min-width: 100%; display: flex; height: 100%; margin: 0px 0px 10px 0px;">
                                <div class="chart chart-small-block" style="width: 40%; height: 30vh;">
                                    <label class="label-chart">Resumo geral</label>
                                    <canvas id="graficoResumoGeral" height="45" width="100"></canvas>
                                    <div id="emptyGraficoResumoGeral" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                                <div class="chart chart-small-block" style="width: 60%; margin: 0 0 0 10px; height: 30vh;">
                                    <label class="label-chart">Tempo paralisado</label>
                                    <canvas id="graficoTempoParalisado" height="30" width="100"></canvas>
                                    <div id="emptyGraficoTempoParalisado" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                        </div>
                        <div style="min-width: 100%; display: flex; height: 100%; margin: 0px 0px 100px 0px;">
                                <div class="chart chart-small-block" style="width: 100%; height: 40vh;">
                                    <label class="label-chart">Total descarregado por dia e período, MT</label>
                                    <canvas id="graficoDescarregadoDiaPeriodo" height="20" width="100"></canvas>
                                    <div id="emptyGraficoDescarregadoDiaPeriodo" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                        </div>
                </section>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="<?php echo $basePath; ?>/js/relatorios/MultiSelect.js"></script>
<script type="module" src="<?php echo $basePath; ?>/js/relatorios/prancha/prancha_charts.js"></script>
<script src="<?php echo $basePath; ?>/js/relatorios/charts_functions.js"></script>

    <!-- Required vendors -->
    <script src="<?php echo $basePath; ?>/vendor/global/global.min.js"></script>
    <script src="<?php echo $basePath; ?>/js/quixnav-init.js"></script>
    <script src="<?php echo $basePath; ?>/js/custom.min.js"></script>
	<script src="<?php echo $basePath; ?>/js/logout.js"></script>
    <script src="<?php echo $basePath; ?>/js/pbi/pbi_report.js"></script>
    
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