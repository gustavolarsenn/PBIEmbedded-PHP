window.addEventListener("load", async function() {
    // Call the generateFilters function here
    generateCharts();
});

function renameKeys(obj, keyMap) {
    return Object.keys(obj).reduce((acc, key) => {
        const newKey = keyMap[key] || key; // Use new key name if it exists in the mapping, otherwise use the original key
        acc[newKey] = obj[key]; // Assign the value to the new key in the accumulator object
        return acc;
    }, {}); // Initial value for the accumulator is an empty object
}

async function generateFilters(campo, filterData){
    const filterField = document.getElementById(`lista-${campo}`);
    const filteredField = document.getElementById(`lista-${campo}`).value;

    newData = await getUniqueData(campo);
    console.log(newData)
    console.log(filterField)
    var filteredData = filterData.filter(item => !newData.includes(item));

    // if (campo === 'navio') {
    //     filterField.innerHTML = `<option value="${filteredData[0].navio}">${filteredData[0].navio}</option>`;
    //     filteredData.shift();
    // } else if (filteredField){
    //     filterField.innerHTML = `<option value="${filteredField}">${filteredField}</option>`;
    //     filteredData.shift();
    // } else {
    //     filterField.innerHTML = '<option value="">Todos</option>';
    // }
    // Example mapping of old key names to new key names

// Function to rename keys in an object based on the provided mapping

    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };
    const renamedFilteredData = filteredData.map(item => renameKeys(item, keyMapping));

    new MultiSelect(`#lista-${campo}`, {
        data: renamedFilteredData,
        placeholder: 'Todos',
        keepOpen: true,
        multiple: true,
        search: true,
        selectAll: true,
        count: true,
        // onChange: function() {
        //     generateCharts();
        // }
    });

    filteredData.forEach((item) => {
        const option = document.createElement("option");
        option.value = Object.values(item)[0];
        option.text = Object.values(item)[0];
        if (option in filteredData) return;
        filterField.appendChild(option);
        });
}

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
var graficoDescarregadoResto, graficoVolumeCliente, graficoVolumeDiaPeriodo, graficoVolumeDia, graficoRealizadoClienteDI, graficoRealizadoPorao;

async function generateCharts() {

    if (graficoDescarregadoResto) graficoDescarregadoResto.destroy();
    if (graficoVolumeCliente) graficoVolumeCliente.destroy();
    if (graficoVolumeDiaPeriodo) graficoVolumeDiaPeriodo.destroy();
    if (graficoVolumeDia) graficoVolumeDia.destroy();
    if (graficoRealizadoClienteDI) graficoRealizadoClienteDI.destroy();
    if (graficoRealizadoPorao) graficoRealizadoPorao.destroy();

    const listaNavio = await getUniqueData('navio');
    const listaPorao = await getUniqueData('porao');
    const listaCliente = await getUniqueData('cliente');
    const listaArmazem = await getUniqueData('armazem');
    const listaProduto = await getUniqueData('produto');
    const listaDI = await getUniqueData('di');

    console.log(listaNavio, listaPorao, listaCliente, listaArmazem, listaProduto, listaDI)

    generateFilters('navio', listaNavio);
    generateFilters('porao', listaPorao);
    generateFilters('cliente', listaCliente);
    generateFilters('armazem', listaArmazem);
    generateFilters('produto', listaProduto);
    generateFilters('di', listaDI);

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

    // 1 - Total descarregado e restante
    const dadosDescarregadoResto = await getDischargingData('totalDescarregado');
    const dadosPlanejado = await getDischargingData('totalPlanejado');
    const noDataGraficoDescarregadoResto = document.getElementById('emptyGraficoDescarregadoResto');
    const dataGraficoDescarregadoResto = document.getElementById('graficoDescarregadoResto');
    
    dataGraficoDescarregadoResto.style.visibility = 'hidden';
    noDataGraficoDescarregadoResto.style.visibility = 'visible';
    if (dadosDescarregadoResto.peso !== null) {
        noDataGraficoDescarregadoResto.style.visibility = 'hidden';
        dataGraficoDescarregadoResto.style.visibility = 'visible';

        graficoDescarregadoResto = new Chart('graficoDescarregadoResto', {
        type: 'doughnut',
        data: {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregadoResto.peso, dadosPlanejado.planejado - dadosDescarregadoResto.peso],
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


    // 2 - Realizado por porão
    const dadosRealizadoPorao = await getDischargingData('descarregadoPorao');
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

    // 3 - Realizado por cliente, armazém e DI
    const dadosRealizadoClienteDI = await getDischargingData('descarregadoClienteArmazemDI');

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
            labels: dadosRealizadoClienteDI.map(d => d.cliente + " - " + d.armazem + " - " + d.di),
            datasets: [{
                label: 'Realizado',
                data: dadosRealizadoClienteDI.map(d => ((d.peso / d.planejado) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: 'rgba(82, 183, 136, 0.5)',
                borderColor: 'rgba(82, 183, 136, 0.65)',
                borderWidth: 1
            },
            {
                label: 'Restante',
                data: dadosRealizadoClienteDI.map(d => ((1 - (d.peso / d.planejado))* 100).toFixed(2)), // Peso descarregado / planejado
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

    // 4 - Volume descarregado por dia
    const dadosVolumeDia = await getDischargingData('descarregadoDia');
    const noDataGraficoVolumeDia = document.getElementById('emptyGraficoVolumeDia');
    const dataGraficoVolumeDia = document.getElementById('graficoVolumeDia');

    dataGraficoVolumeDia.style.visibility = 'hidden';
    noDataGraficoVolumeDia.style.visibility = 'visible';
    if (dadosVolumeDia.length > 0) {
        noDataGraficoVolumeDia.style.visibility = 'hidden';
        dataGraficoVolumeDia.style.visibility = 'visible';

    
    const ctx = dataGraficoVolumeDia.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 1)");

    
    var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
    gradientFill.addColorStop(1, "rgba(128, 182, 244, 0.3)");
    gradientFill.addColorStop(0, "rgba(61, 68, 101, 0.3)");

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
                pointRadius: 2,
                fill: true,
                borderWidth: 1,
                lineTension: 0
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
                    },
                }]
            },
            legend: {
                display: false
            },
            layout: {
                padding: {
                    top: 5,
            }
        },
            responsive: true,
            maintainAspectRatio: false
        }
    });
    }


    // 5 - Volume descarregado por cliente
    const dadosVolumeCliente = await getDischargingData('descarregadoCliente');
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
    const dadosVolumeDiaPeriodo = await getDischargingData('descarregadoDiaPeriodo');

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

    // 6 - Volume descarregado por dia e período
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

    }
}

async function getDischargingData(agrupamento){

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroData = Array.from(document.getElementById('data').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroPorao = Array.from(document.getElementById('lista-porao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroCliente = Array.from(document.getElementById('lista-cliente').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroArmazem = Array.from(document.getElementById('lista-armazem').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroProduto = Array.from(document.getElementById('lista-produto').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroDI = Array.from(document.getElementById('lista-di').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');

    console.log(filtroCliente)

    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [{
            name: 'action',
            value: agrupamento
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

async function getUniqueData(campo){

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroData = Array.from(document.getElementById('data').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroPorao = Array.from(document.getElementById('lista-porao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroCliente = Array.from(document.getElementById('lista-cliente').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroArmazem = Array.from(document.getElementById('lista-armazem').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroProduto = Array.from(document.getElementById('lista-produto').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');
    const filtroDI = Array.from(document.getElementById('lista-di').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)//.join(', ');

    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [{
            name: 'action',
            value: 'readUnique'
        }, {
            name: 'campo',
            value: campo
        },{
            name: 'where',
            value: JSON.stringify({
                navio: filtroNavio ? filtroNavio : null,
                data: filtroData ? filtroData : null,
                periodo: filtroPeriodo ? filtroPeriodo : null,
                porao: filtroPorao ? filtroPorao : null,
                cliente: filtroCliente ? filtroCliente : null,
                armazem: filtroArmazem ? filtroArmazem : null,
                produto: filtroProduto ? filtroProduto : null,
                di: filtroDI ? filtroDI : null,
                peso: 0
            })
        
        }],
        dataType: 'json'
    };

    console.log(request)

    // Return a new Promise
    return new Promise((resolve, reject) => {
        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
                reject(response.error);
            } else {
                console.log(response.query)
                resolve(response.data);
            }
        }).fail(function(response) {
            console.log(response.query)
            console.log(response)
            reject(response.error);
        })
    });

}