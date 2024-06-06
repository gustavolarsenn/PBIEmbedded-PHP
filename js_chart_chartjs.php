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
    <link rel="stylesheet" href="./css/pbi_reports.css">
</head>
<style>

</style>

<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
<script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
<script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>
<!-- <script src="charts.js"></script> -->

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
    <table>
        <tr style="width: 100%; max-width: 100vw">
            <th style="width: 25%"><canvas id="container_three" style="height: 200px; width:100%"></canvas></th>
            <th style="width: 100%"><canvas id="container_two" style="height: 250px; width:100%"></canvas></th>
        </tr>
        <tr>
            <th><canvas id="container_one" style="height: 250px; width:100%;max-width:500px"></canvas></th>
            <th><canvas id="container_four" style="height: 250px; width:100%;max-width:500px"></canvas></th>
            <th><canvas id="container_five" style="height: 250px; width:100%;max-width:500px"></canvas></th>
        </tr>
    </table>

    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="module">

async function getDischargingData(select, group_by, order_by, limit, where, column_agg, type_agg){
    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [{
            name: 'action',
            value: 'readQuery'
        }, {
            name: 'select',
            value: select
        }, {
            name: 'group_by',
            value: group_by
        }, {
            name: 'order_by',
            value: order_by
        }, {
            name: 'limit',
            value: limit
        }, {
            name: 'where',
            value: where
        }, {
            name: 'column_agg',
            value: column_agg
        }, {
            name: 'type_agg',
            value: type_agg
        }],
        dataType: 'json'
    };

    // Return a new Promise
    return new Promise((resolve, reject) => {
        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
                reject(response.error);
            } else {
                resolve(response.data);
            }
        }).fail(function(response) {
            console.log(response)
            reject(response.error);
        })
    });
}

// Use an IIFE (Immediately Invoked Function Expression) to use await at the top level
(async function() {
    const data = await getDischargingData('cliente,', 'cliente', 'cliente DESC', null, null, 'peso', 'SUM');

    const barOptions = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    display: false
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            },
            responsive: true,
        }

    const horizontalBarOptions = {
        scales: {
            xAxes: [{
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                display: false,
                stacked: true
            }],
            yAxes: [{
                gridLines: {
                    display: false
                },
                stacked: true
            }]
        },
        legend: {
            display: false
        },
        responsive: true,
    }
    let firstBarChartOptions = JSON.parse(JSON.stringify(barOptions)); // Deep copy
    let secondBarChartOptions = JSON.parse(JSON.stringify(barOptions)); // Deep copy

    secondBarChartOptions.legend.display = true;


    const firstChart = new Chart('container_one', {
        type: 'bar',
        data: {
            labels: data.map(d => d.cliente),
            datasets: [{
                label: 'Peso',
                data: data.map(d => d.peso),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
                
            }]
        },
        options: firstBarChartOptions
    });


    // // Assume you have a second set of data
    const data2 = await getDischargingData('CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)) AS data, periodo,', 'CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)), periodo', 'CAST(data AS date) ASC', null, null, 'peso', 'SUM');

    // Group data by 'periodo'
    const groupedData = data2.reduce((acc, d) => {
        acc[d.periodo] = acc[d.periodo] || [];
        acc[d.periodo].push(d);
        return acc;
    }, {});


    // Create a unique set of 'data' values
    const uniqueDataValues = [...new Set(data2.map(d => d.data))];

    // Create a dataset for each 'periodo'
    const datasets = Object.keys(groupedData).map((periodo, i) => {
        const data = uniqueDataValues.map(d => {
            const match = groupedData[periodo].find(x => x.data === d);
            return match ? match.peso : null;
        });

        return {
            label: periodo,
            data: data,
            backgroundColor: `rgba(${255 - i * 30}, ${99 + i * 30}, ${132 + i * 30}, 0.2)`,
            borderColor: `rgba(${255 - i * 30}, ${99 + i * 30}, ${132 + i * 30}, 1)`,
            borderWidth: 1
        };
    });

    const secondChart = new Chart('container_two', {
        type: 'bar',
        data: {
            labels: uniqueDataValues,
            datasets: datasets
        },
        options: secondBarChartOptions
    });

    const data3 = await getDischargingData(null, null, null, null, null, 'peso', 'SUM');

    const thirdChart = new Chart('container_three', {
        type: 'doughnut',
        data: {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [data3[0].peso, 40000000 - data3[0].peso],
                backgroundColor: [
                    'rgba(80, 200, 120, 0.2)',
                    'rgba(54, 162, 235, 0.05)'
                ],
                borderColor: [
                    'rgba(80, 200, 120, 0.5)'
                ],
            }]
        },
        options: {
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        }
    });

const data4 = await getDischargingData('CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)) AS data,', 'CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data))', 'CAST(data AS date) ASC', null, null, 'peso', 'SUM');

const fourthChart = new Chart('container_four', {
        type: 'line',
        data: {
            labels: data4.map(d => d.data),
            datasets: [{
                label: 'Peso',
                data: data4.map(d => d.peso),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    display: false
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            },
            responsive: true,
        }
    });


const data5 = await getDischargingData('cliente, di,', 'cliente, di', 'cliente, di DESC', null, null, 'peso', 'SUM');

console.log(data5)

const fifthChart = new Chart('container_five', {
    type: 'horizontalBar',
    data: {
        labels: data5.map(d => d.cliente + " - " + d.di),
        datasets: [{
            label: 'Realizado',
            data: data5.map(d => ((d.peso / (d.peso + 1000000)) * 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: 'rgba(80, 200, 120, 0.5)',
            borderColor: 'rgba(80, 200, 120, 0.5)',
            borderWidth: 1
        },
        {
            label: 'Restante',
            data: data5.map(d => ((1 - (d.peso / (d.peso + 1000000)))* 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: 'rgba(54, 162, 235, 0.05)',
            borderColor: 'rgba(54, 162, 235, 0.5)',
            borderWidth: 1
        }
    ]
    },
    options: 
       {...horizontalBarOptions, 
        legend: {
            display: true
        }
       },
});

})();

// Declare the chart dimensions and margins.

    </script>



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