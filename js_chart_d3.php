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
    #all-containers{
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 50px;
        width: 100%;
        height: 100%;
        min-height: 100vh;
        min-width: 100vw;
    }
    #container-1{
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 50px;
    }
    .x-axis, .y-axis{
        font-size: 15px;
        color: black;
        font-weight: bolder;
    }

    div.tooltip-donut {
     position: absolute;
     font-size: medium;
     background-color: rgb(0, 0, 0, 0.5);
     padding: .9rem;
     width: auto;
     color: rgb(255, 255, 255, 0.95);
    box-shadow: 0 0 5px #000;
     pointer-events: all;
}
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

        <section id='all-containers'>
        <div id='container_one'></div>
        <div id='container_two'></div>
            <!-- <p id="error-message"></p> -->
        


    </div>

<script type="module">
import * as d3 from "https://cdn.jsdelivr.net/npm/d3@7/+esm";

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
const data = await getDischargingData('cliente', 'cliente', 'cliente DESC', null, null, 'peso', 'SUM');

    console.log(data);
// Select the parent container and get its width and height
var container = d3.select('#all-containers');
var containerWidth = container.node().getBoundingClientRect().width;
var containerHeight = container.node().getBoundingClientRect().height;

// Define the margins as a percentage of the container size
const marginTop = containerHeight * 0.1; // 10% of the container height
const marginRight = containerWidth * 0.2; // 2% of the container width
const marginBottom = containerHeight * 0.1; // 3% of the container height
const marginLeft = containerWidth * 0.15; // 40% of the container width

// Define the width and height based on the container size and margins
const width = containerWidth - marginLeft - marginRight;
const height = containerHeight - marginTop - marginBottom;

    var div = d3.select("body").append("div")
        .attr("class", "tooltip-donut")
        .style("opacity", '0');

    const x = d3.scaleBand()
        .domain(data.map(d => d.cliente))
        .range([marginLeft, width - marginRight])
        .padding(0.1);

    // Declare the y (vertical position) scale.
    const y = d3.scaleLinear()
        .domain([0, (d3.max(data, d => d.peso) + (d3.max(data, d => d.peso) * 0.1))])
        .range([height - marginBottom, marginTop]);

    // Create the SVG container.
    var svg = d3.create("svg")
        .attr("width", width)
        .attr("height", height)

    // Add the bars.
    svg.selectAll("rect")
        .data(data)
        .join("rect")
        .attr("x", d => x(d.cliente))
        .attr("y", d => y(d.peso))
        .attr("width", x.bandwidth())
        .attr("height", d => y(0) - y(d.peso))
        .attr("fill", "steelblue")
     .on('mouseover', function (event, d){
          d3.select(this).transition()
               .duration(50)
               .attr('opacity', '.85');
          div.transition()
               .duration(50)
               .style("opacity", '1');
          div.html('Cliente: ' + d.cliente + '<br>' + 'Peso: ' + d.peso + ' kg')
               .style("left", (event.pageX + 10) + "px")
               .style("top", (event.pageY - 15) + "px");
     })
     .on('mouseout', function (d, i) {
          d3.select(this).transition()
               .duration(50)
               .attr('opacity', '1');
          div.transition()
               .duration(50)
               .style("opacity", '0');
     });

    // Add the x-axis.
    svg.append("g")
        .attr('class', 'x-axis')
        .attr("transform", `translate(0,${height - marginBottom})`)
        .call(d3.axisBottom(x));

    // Add the y-axis.
    svg.append("g")
        .attr('class', 'y-axis')
        .attr("transform", `translate(${marginLeft},0)`)
        .call(d3.axisLeft(y));

    // Append the SVG element.
    container_one.append(svg.node());


// // Assume you have a second set of data
const data2 = await getDischargingData('CONCAT(MONTH(data), "/", YEAR(data)) AS data, periodo', 'CONCAT(MONTH(data), "/", YEAR(data)), periodo', 'SUM(peso) DESC', null, null, 'peso', 'SUM');
console.log(data2)

// // Define the scales, axes, and SVG container for the second chart
// Assuming you have two columns 'data' and 'anotherData' in your data2
const x2 = d3.scaleBand()
    .domain(data2.map(d => d.data))
    .range([marginLeft, width - marginRight])
    .padding(0.1);

const xSubgroup = d3.scaleBand()
    .domain(data2.map(d => d.periodo))
    .range([0, x2.bandwidth()])
    .padding(0.05);

const y2 = d3.scaleLinear()
    .domain([0, (d3.max(data2, d => Math.max(d.peso)) + (d3.max(data2, d => Math.max(d.peso)) * 0.1))])
    .range([height - marginBottom, marginTop]);

const color = d3.scaleOrdinal()
    .domain(data2.map(d => d.periodo))
    .range(['#e41a1c', '#377eb8', '#4daf4a', '#984ea3'])
    .unknown('#ccc');

var svg2 = d3.create("svg")
    .attr("width", width)
    .attr("height", height);

// Add the bars for the second chart
svg2.append("g")
    .selectAll("g")
    .data(data2)
    .enter()
    .append("g")
    .attr("transform", d => `translate(${x2(d.data)}, 0)`)
    .selectAll("rect")
    .data(d => ['peso'].map(key => ({key: d.periodo, value: d[key]}))) // change here
    .enter().append("rect")
    .attr("x", d => xSubgroup(d.key))
    .attr("y", d => y2(d.value))
    .attr("width", xSubgroup.bandwidth())
    .attr("height", d => y2(0) - y2(d.value))
    .attr("fill", d => color(d.key))
    .on('mouseover', function (event, d){
          d3.select(this).transition()
               .duration(50)
               .attr('opacity', '.85');
          div.transition()
               .duration(50)
               .style("opacity", '1');
          div.html('Cliente: ' + d.key + '<br>' + 'Peso: ' + d.value + ' kg')
               .style("left", (event.pageX + 10) + "px")
               .style("top", (event.pageY - 15) + "px");
     })
     .on('mouseout', function (d, i) {
          d3.select(this).transition()
               .duration(50)
               .attr('opacity', '1');
          div.transition()
               .duration(50)
               .style("opacity", '0');
     });
// Add the x-axis for the second chart
svg2.append("g")
    .attr('class', 'x-axis')
    .attr("transform", `translate(0,${height - marginBottom})`)
    .call(d3.axisBottom(x2));

// Add the y-axis for the second chart
svg2.append("g")
    .attr('class', 'y-axis')
    .attr("transform", `translate(${marginLeft},0)`)
    .call(d3.axisLeft(y2));

var legend = svg2.append("g")
    .attr("font-family", "sans-serif")
    .attr("font-size", 10)
    .attr("text-anchor", "end")
    .selectAll("g")
    .data(color.domain().slice().reverse())
    .enter().append("g")
    .attr("transform", (d, i) => `translate(0,${i * 20})`);

legend.append("rect")
    .attr("x", 50)
    .attr("width", 19)
    .attr("height", 19)
    .attr("fill", color);

legend.append("text")
    .attr("x", 125)
    .attr("y", 9.5)
    .attr("dy", "0.32em")
    .text(d => d);

// Append the SVG element for the second chart
container_two.append(svg2.node());


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