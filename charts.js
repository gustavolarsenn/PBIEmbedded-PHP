window.addEventListener("load", async function() {
    const listaPorao = await getUniqueData('porao');
    const listaCliente = await getUniqueData('cliente');
    const listaArmazem = await getUniqueData('armazem');
    const listaProduto = await getUniqueData('produto');
    const listaDI = await getUniqueData('di');

    generateFilters('porao', listaPorao);
    generateFilters('cliente', listaCliente);
    generateFilters('armazem', listaArmazem);
    generateFilters('produto', listaProduto);
    generateFilters('di', listaDI);

    // Call the generateFilters function here
    generateCharts();
});

const hamburger = document.querySelector('.hamburger');

function collapsedMenu() {
    
}

hamburger.addEventListener('click', () => {})

console.log(hamburger);

function cleanFilters(){
    document.getElementById('lista-navio').value = '';
    document.getElementById('data').value = '';
    document.getElementById('lista-periodo').value = '';
    document.getElementById('lista-porao').value = '';
    document.getElementById('lista-cliente').value = '';
    document.getElementById('lista-armazem').value = '';
    document.getElementById('lista-produto').value = '';
    document.getElementById('lista-di').value = '';

    generateCharts();
}

async function getUniqueData(campo){
    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [{
            name: 'action',
            value: 'readUnique'
        }, {
            name: 'campo',
            value: campo
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

async function getDischargingData(select, group_by, order_by, limit, where, column_agg, type_agg){

    const filtroNavio = document.getElementById('lista-navio').value;
    const filtroData = document.getElementById('data').value;
    const filtroPeriodo = document.getElementById('lista-periodo').value;
    const filtroPorao = document.getElementById('lista-porao').value;
    const filtroCliente = document.getElementById('lista-cliente').value;
    const filtroArmazem = document.getElementById('lista-armazem').value;
    const filtroProduto = document.getElementById('lista-produto').value;
    const filtroDI = document.getElementById('lista-di').value;

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
            value: JSON.stringify({
                navio: filtroNavio ? filtroNavio : null,
                data: filtroData ? filtroData : null,
                periodo: filtroPeriodo ? filtroPeriodo : null,
                porao: filtroPorao ? filtroPorao : null,
                cliente: filtroCliente ? filtroCliente : null,
                armazem: filtroArmazem ? filtroArmazem : null,
                produto: filtroProduto ? filtroProduto : null,
                di: filtroDI ? filtroDI : null
            })
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

var graficoDescarregadoResto, graficoVolumeCliente, graficoVolumeDiaPeriodo, graficoVolumeDia, graficoRealizadoClienteDI, graficoRealizadoPorao;

async function generateCharts() {

    if (graficoDescarregadoResto) graficoDescarregadoResto.destroy();
    if (graficoVolumeCliente) graficoVolumeCliente.destroy();
    if (graficoVolumeDiaPeriodo) graficoVolumeDiaPeriodo.destroy();
    if (graficoVolumeDia) graficoVolumeDia.destroy();
    if (graficoRealizadoClienteDI) graficoRealizadoClienteDI.destroy();
    if (graficoRealizadoPorao) graficoRealizadoPorao.destroy();

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

    const dadosVolumeCliente = await getDischargingData('cliente,', 'cliente', 'cliente DESC', null, null, 'peso', 'SUM');
    const noDataGraficoVolumeCliente = document.getElementById('emptyGraficoVolumeCliente');
    const dataGraficoVolumeCliente = document.getElementById('graficoVolumeCliente');

    dataGraficoVolumeCliente.style.visibility = 'hidden';
    noDataGraficoVolumeCliente.style.visibility = 'visible';
    if (dadosVolumeCliente.length > 0) {
        noDataGraficoVolumeCliente.style.visibility = 'hidden';
        dataGraficoVolumeCliente.style.visibility = 'visible';

        graficoVolumeCliente = new Chart('graficoVolumeCliente', {
            type: 'horizontalBar',
            data: {
                labels: dadosVolumeCliente.map(d => d.cliente),
                datasets: [{
                    label: 'Peso',
                    data: dadosVolumeCliente.map(d => d.peso),
                    backgroundColor: 'rgba(61, 68, 101, 0.8)',
                    borderColor: 'rgba(61, 68, 101, 1)',
                    borderWidth: 1
                    
                }]
            },
            options: {...horizontalBarOptions,
                maintainAspectRatio: false
            }

        });
    }

    // // Assume you have a second set of data
    const dadosVolumeDiaPeriodo = await getDischargingData('CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)) AS data, periodo,', 'CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)), periodo', 'CAST(data AS date) ASC', null, null, 'peso', 'SUM');

    // Group data by 'periodo'
    const dadosAgrupadosDiaPeriodo = dadosVolumeDiaPeriodo.reduce((acc, d) => {
        acc[d.periodo] = acc[d.periodo] || [];
        acc[d.periodo].push(d);
        return acc;
    }, {});


    // Create a unique set of 'data' values
    const datasUnicas = [...new Set(dadosVolumeDiaPeriodo.map(d => d.data))];

    // Create a dataset for each 'periodo'
    const datasets = Object.keys(dadosAgrupadosDiaPeriodo).map((periodo, i) => {
        const data = datasUnicas.map(d => {
            const match = dadosAgrupadosDiaPeriodo[periodo].find(x => x.data === d);
            return match ? match.peso : null;
        });

        return {
            label: periodo,
            data: data,
            backgroundColor: `rgba(${255 - i * 30}, ${99 + i * 30}, ${132 + i * 30}, 0.8)`,
            borderColor: `rgba(${255 - i * 30}, ${99 + i * 30}, ${132 + i * 30}, 1)`,
            borderWidth: 1
        };
    });

    const noDataGraficoVolumeDiaPeriodo = document.getElementById('emptyGraficoVolumeDiaPeriodo');
    const dataGraficoVolumeDiaPeriodo = document.getElementById('graficoVolumeDiaPeriodo');

    dataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
    noDataGraficoVolumeDiaPeriodo.style.visibility = 'visible';
    if (dadosVolumeDiaPeriodo.length > 0) {
        noDataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
        dataGraficoVolumeDiaPeriodo.style.visibility = 'visible';

    graficoVolumeDiaPeriodo = new Chart('graficoVolumeDiaPeriodo', {
        type: 'bar',
        data: {
            labels: datasUnicas,
            datasets: datasets
        },
        options: {...secondBarChartOptions,
            maintainAspectRatio: false
        }
    });
}

    const dadosDescarregadoResto = await getDischargingData(null, null, null, null, null, 'peso', 'SUM');
    const noDataGraficoDescarregadoResto = document.getElementById('emptyGraficoDescarregadoResto');
    const dataGraficoDescarregadoResto = document.getElementById('graficoDescarregadoResto');

    dataGraficoDescarregadoResto.style.visibility = 'hidden';
    noDataGraficoDescarregadoResto.style.visibility = 'visible';
    if (dadosDescarregadoResto[0].peso !== null) {
        noDataGraficoDescarregadoResto.style.visibility = 'hidden';
        dataGraficoDescarregadoResto.style.visibility = 'visible';

        graficoDescarregadoResto = new Chart('graficoDescarregadoResto', {
        type: 'doughnut',
        data: {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregadoResto[0].peso, 40000000 - dadosDescarregadoResto[0].peso],
                backgroundColor: [
                    'rgba(82, 183, 136, 0.5)',
                    'rgba(54, 162, 235, 0.05)'
                ],
                borderColor: [
                    'rgba(82, 183, 136, 0.6)'
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
    }
    const dadosVolumeDia = await getDischargingData('CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data)) AS data,', 'CONCAT(LPAD(DAY(data), 2, "0"), "/",LPAD(MONTH(data), 2, "0"), "/",YEAR(data))', 'CAST(data AS date) ASC', null, null, 'peso', 'SUM');
    const noDataGraficoVolumeDia = document.getElementById('emptyGraficoVolumeDia');
    const dataGraficoVolumeDia = document.getElementById('graficoVolumeDia');

    dataGraficoVolumeDia.style.visibility = 'hidden';
    noDataGraficoVolumeDia.style.visibility = 'visible';
    if (dadosVolumeDia.length > 0) {
        noDataGraficoVolumeDia.style.visibility = 'hidden';
        dataGraficoVolumeDia.style.visibility = 'visible';


    const ctx = noDataGraficoVolumeDia.value.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(0, '#80b6f4');
    gradientStroke.addColorStop(1, '#f49080');
    
    var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
    gradientFill.addColorStop(0, "rgba(128, 182, 244, 0.6)");
    gradientFill.addColorStop(1, "rgba(244, 144, 128, 0.6)");

    graficoVolumeDia = new Chart('graficoVolumeDia', {
        type: 'line',
        data: {
            labels: dadosVolumeDia.map(d => d.data),
            datasets: [{
                label: 'Peso',
                data: dadosVolumeDia.map(d => d.peso),
                backgroundColor: gradientFill,
                borderColor: gradientStroke,
                pointBorderColor: gradientStroke,
                pointBackgroundColor: gradientStroke,
                pointHoverBackgroundColor: gradientStroke,
                pointHoverBorderColor: gradientStroke,
                pointBorderWidth: 10,
                pointHoverRadius: 10,
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                fill: true,
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
            maintainAspectRatio: false
        }
    });
    }

    const dadosRealizadoClienteDI = await getDischargingData('cliente, di,', 'cliente, di', 'cliente, di DESC', null, null, 'peso', 'SUM');
    const noDataGraficoRealizadoClienteDI = document.getElementById('emptyGraficoRealizadoClienteDI');
    const dataGraficoRealizadoClienteDI = document.getElementById('graficoRealizadoClienteDI');

    dataGraficoRealizadoClienteDI.style.visibility = 'hidden';
    noDataGraficoRealizadoClienteDI.style.visibility = 'visible';

    if (dadosRealizadoClienteDI.length > 0) {
        noDataGraficoRealizadoClienteDI.style.visibility = 'hidden';
        dataGraficoRealizadoClienteDI.style.visibility = 'visible';

    graficoRealizadoClienteDI = new Chart('graficoRealizadoClienteDI', {
        type: 'horizontalBar',
        data: {
            labels: dadosRealizadoClienteDI.map(d => d.cliente + " - " + d.di),
            datasets: [{
                label: 'Realizado',
                data: dadosRealizadoClienteDI.map(d => ((d.peso / (d.peso + 1000000)) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: 'rgba(82, 183, 136, 0.5)',
                borderColor: 'rgba(82, 183, 136, 0.65)',
                borderWidth: 1
            },
            {
                label: 'Restante',
                data: dadosRealizadoClienteDI.map(d => ((1 - (d.peso / (d.peso + 1000000)))* 100).toFixed(2)), // Peso descarregado / planejado
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
    }
    const dadosRealizadoPorao = await getDischargingData('porao,', 'porao', 'porao ASC', null, null, 'peso', 'SUM');
    const noDataRealizadoPorao = document.getElementById('emptyGraficoRealizadoPorao');
    const dataGraficoRealizadoPorao = document.getElementById('graficoRealizadoPorao');

    dataGraficoRealizadoPorao.style.visibility = 'hidden';
    noDataRealizadoPorao.style.visibility = 'visible';
    if(dadosRealizadoPorao.length > 0){
        noDataRealizadoPorao.style.visibility = 'hidden';
        dataGraficoRealizadoPorao.style.visibility = 'visible';

        graficoRealizadoPorao = new Chart('graficoRealizadoPorao', {
        type: 'horizontalBar',
        data: {
            labels: dadosRealizadoPorao.map(d => d.porao),
            datasets: [{
                label: 'Realizado',
                data: dadosRealizadoPorao.map(d => ((d.peso / (d.peso + 1000000)) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: 'rgba(82, 183, 136, 0.5)',
                borderColor: 'rgba(82, 183, 136, 0.8)',
                borderWidth: 1
            },
            {
                label: 'Restante',
                data: dadosRealizadoPorao.map(d => ((1 - (d.peso / (d.peso + 1000000)))* 100).toFixed(2)), // Peso descarregado / planejado
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
    }
}
function generateFilters(campo, filterData){
    const filterField = document.getElementById(`lista-${campo}`);
    
    filterData.forEach((item) => {
        const option = document.createElement("option");
        option.value = Object.values(item)[0];
        option.text = Object.values(item)[0];
        filterField.appendChild(option);
        });
}
