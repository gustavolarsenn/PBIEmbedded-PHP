<?php
require 'SessionManager.php';


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
    <title>Zport</title>

    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.theme.default.min.css">
    <link href="./vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/charts.css">
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
                <img class="logo-compact" src="./images/logo-zport-branca-3x.png" alt="">
                <img class="brand-title" src="./images/logo-zport-branca-3x.png" alt="">
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
                        <li><a href="cadNavios.php">Navios</a></li>	
						<li><a href="cliente.php">Clientes</a></li>
						<li><a href="carga.php">Carga</a></li>
                        </ul>
                    </li>
					
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i class="icon icon-form"></i><span class="nav-text">Inclusão</span></a>
                        <ul aria-expanded="false">
                        <li><a href="Escala1.php">Escala</a></li>
						<li><a href="paralizacao.php">Paralizações</a></li>
						<li><a href="periodosTrabalhados.php">Periodo Trabalhado</a></li>
						<li><a href="planoDistribuicao.php">Plano de Distribuição</a></li>
						
                        </ul>
                    </li>
				
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                                class="icon icon-layout-25"></i><span class="nav-text">Relatórios</span></a>
                        <ul aria-expanded="false">
                        <li><a href="relatorioEscala1.php">Relatório de Escala</a></li>	
						<li><a href="solRelatorioDescarga1.php">Relatório por periodo</a></li>
						<li><a href="solRelatorioCliente.php">Relatório por cliente</a></li>
						<li><a href="cadChuvaNavio.php">Relatório Chuva</a></li>
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
                        <svg xmlns="http://www.w3.org/2000/svg" onclick='cleanFilters()' width="30" height="30" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16" style="margin: auto">
                            <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293z"/>
                        </svg>
                    </div>
                    <div class="input-label">
                        <label>Navio</label>
                        <select id='lista-navio'></select>
                    </div>
                    <div class="input-label">
                        <label>Data</label>
                        <input type="date" id='data'>
                    </div>
                    <div class="input-label">
                        <label>Periodo</label>
                        <select id='lista-periodo'>
                            <option value="">Todos</option>
                            <option value='01:00x07:00'>01:00x07:00</option>
                            <option value='07:00x13:00'>07:00x13:00</option>
                            <option value='13:00x19:00'>13:00x19:00</option>
                            <option value='19:00x01:00'>19:00x01:00</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <label>Porao</label>
                        <select id="lista-porao">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <label>Cliente</label>
                        <select id="lista-cliente">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <label>Armazém</label>
                        <select id="lista-armazem">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <label>Produto</label>
                        <select id="lista-produto">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <label>DI</label>
                        <select id="lista-di">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="input-label">
                        <input type="button" value="Filtrar" onclick="generateCharts()">
                    </div>
                </div>
                <section>
                        <div style="min-width: 100%; display: flex;">
                            <div style="width: 30%; max-height: 60vh; min-height: fit-content; margin-left: 10px;">
                                <div class="chart chart-small-block" style="margin-bottom: 10px;">
                                    <label class="label-chart">Total descarregado / Restante</label>
                                    <canvas id="graficoDescarregadoResto" class='any-chart'></canvas>
                                    <div id="emptyGraficoDescarregadoResto" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                                <div class="chart chart-small-block">
                                    <label class="label-chart">Descarregamento por porão</label>
                                    <canvas id="graficoRealizadoPorao" class='any-chart'></canvas>
                                    <div id="emptyGraficoRealizadoPorao" class="no-data">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                            </div>
                                <div class="chart" style="width: 70%; height: 60vh; margin: 0 10px;">
                                    <label class="label-chart">Volume por cliente e DI</label>
                                    <canvas id="graficoRealizadoClienteDI" class='any-chart' ></canvas>
                                    <div id="emptyGraficoRealizadoClienteDI" class="no-data" style="min-height: 7vh; height: 10vh;">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                                </div>
                        </div>

                        <div style="height: 30vh; margin: 10px; display: flex;">
                            <div class="chart half-chart" style="margin-right: 10px">
                                <label class="label-chart">Descarregado por dia</label>
                                <canvas id="graficoVolumeDia" class="any-chart" style="max-height: 95%"></canvas>
                                <div id="emptyGraficoVolumeDia" class="no-data" style="min-height: 3vh; height: auto;">
                                    <p>Nenhum valor encontrado!</p>
                                </div>
                            </div>
                            <div class="chart half-chart" >
                                <label class="label-chart">Volume por cliente</label>
                                <canvas id="graficoVolumeCliente" class="any-chart" style="max-height: 95%"></canvas>
                                <div id="emptyGraficoVolumeCliente" class="no-data" style="min-height: 3vh; height: auto;">
                                    <p>Nenhum valor encontrado!</p>
                                </div>
                            </div>
                        </div>
                            <div style="margin: 10px;">
                                <div class="chart full-chart" style="height: 40vh; display: grid;">
                                    <label class="label-chart">Descarregamento por dia e período</label>
                                    <canvas id="graficoVolumeDiaPeriodo" class="any-chart" style="max-height: 95%;"></canvas>
                                    <div id="emptyGraficoVolumeDiaPeriodo" class="no-data" style="max-height: 95%;">
                                        <p>Nenhum valor encontrado!</p>
                                    </div>
                            </div>

                </section>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="module">

// Declare the chart dimensions and margins.
    </script>
    <script src="charts.js"></script>

    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
    <script src="./js/quixnav-init.js"></script>
    <script src="./js/custom.min.js"></script>
	<script src="./js/logout.js"></script>
    
    <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
    
	<!-- Form validate init -->
    <script src="./js/plugins-init/jquery.validate-init.js"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="./vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="./js/plugins-init/chartjs-init.js"></script>

    <!-- Form step init -->
    <script src="./js/plugins-init/jquery-steps-init.js"></script>

    </body>
</html>