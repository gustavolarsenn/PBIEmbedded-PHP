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
    <title>Zport</title>

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
                <h1 id="nome-navio"></h1>
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
                                <canvas id="graficoVolumeDia" height="22" width="50"></canvas>
                                <div id="emptyGraficoVolumeDia" class="no-data">
                                    <p>Nenhum valor encontrado!</p>
                                </div>
                            </div>
                            <div id='volume-cliente-grafico' class="chart half-chart">
                                <label class="label-chart">Volume por cliente</label>
                                <canvas id="graficoVolumeCliente" height="22" width="50"></canvas>
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

<script src="<?php echo $basePath; ?>/js/relatorios/MultiSelect.js"></script>
<script type="module" src="<?php echo $basePath; ?>/js/relatorios/balanca/balanca_charts.js"></script>
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