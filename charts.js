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
    
    // const newData = await getUniqueData(campo);

    // try {
    //     var filteredData = filterData.filter(item => !newData.includes(item));
    // } catch (error) {
    //     console.log(error, 'Undefined', campo)
    // }
    
    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };

    let filteredData = filterData.map(item => ({ 0: item, [campo]: item }));
    const renamedFilteredData = filteredData.map(item => renameKeys(item, keyMapping));

    new MultiSelect(`#lista-${campo}`, {
        data: renamedFilteredData,
        placeholder: 'Todos',
        keepOpen: true,
        multiple: true,
        search: true,
        selectAll: true,
        count: true,
    });

    filteredData.forEach((item) => {
        const option = document.createElement("option");
        option.value = Object.values(item)[0];
        option.text = Object.values(item)[0];
        
        if (option in filteredData) return;
        filterField.appendChild(option);
        });
}

// function cleanFilters(){
//     document.getElementById('lista-navio').innerHTML = '';
//     document.getElementById('data').innerHTML = '';
//     document.getElementById('lista-periodo').innerHTML = '';
//     document.getElementById('lista-porao').innerHTML = '';
//     document.getElementById('lista-cliente').innerHTML = '';
//     document.getElementById('lista-armazem').innerHTML = '';
//     document.getElementById('lista-produto').innerHTML = '';
//     document.getElementById('lista-di').innerHTML = '';

//     generateCharts();
// }
var graficoDescarregadoResto, graficoVolumeCliente, graficoVolumeDiaPeriodo, graficoVolumeDia, graficoRealizadoClienteDI, graficoRealizadoPorao;

async function generateCharts() {
    
    const listaNavio = await getUniqueVessels();

    // Map through listaNavio, convert each object's values to a Set to remove duplicates, then convert back to array
    const arrayNaviosUnicos = listaNavio.map(obj => [...new Set(Object.values(obj))]);
    
    // Flatten the array of arrays to get a single array with all values
    const listaNaviosUnicos = arrayNaviosUnicos.flat();

    const rawDate = document.getElementById('data').value;
    const filtroData = rawDate === '' ? null : [new Date(rawDate).toISOString().replace('T', ' ').substring(0, 19)];

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPorao = Array.from(document.getElementById('lista-porao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroCliente = Array.from(document.getElementById('lista-cliente').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroArmazem = Array.from(document.getElementById('lista-armazem').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroProduto = Array.from(document.getElementById('lista-produto').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroDI = Array.from(document.getElementById('lista-di').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)

    const navioSelecionado = filtroNavio.length > 0 ? filtroNavio[0] : listaNavio[0].navio;

    const dataDischarged = await getVesselData('discharged', navioSelecionado);
    const dataPlanned = await getVesselData('planned', navioSelecionado);

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataDischarged = dataDischarged.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = filtroPeriodo.length === 0 || filtroPeriodo.includes(`'${item.periodo}'`);
        const matchesPorao = filtroPorao.length === 0 || filtroPorao.includes(`'${item.porao}'`);
        const matchesCliente = filtroCliente.length === 0 || filtroCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = filtroArmazem.length === 0 || filtroArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = filtroProduto.length === 0 || filtroProduto.includes(`'${item.produto}'`);
        const matchesDI = filtroDI.length === 0 || filtroDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesData && matchesPeriodo && matchesPorao && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataPlanned = dataPlanned.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesCliente = filtroCliente.length === 0 || filtroCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = filtroArmazem.length === 0 || filtroArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = filtroProduto.length === 0 || filtroProduto.includes(`'${item.produto}'`);
        const matchesDI = filtroDI.length === 0 || filtroDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    // const listaNavio = [...new Set(filteredData.map(d => d.navio))];
    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))];
    const listaPorao = [...new Set(filteredDataDischarged.map(d => d.porao))];
    const listaCliente = [...new Set(filteredDataDischarged.map(d => d.cliente))];
    const listaArmazem = [...new Set(filteredDataDischarged.map(d => d.armazem))];
    const listaProduto = [...new Set(filteredDataDischarged.map(d => d.produto))];
    const listaDI = [...new Set(filteredDataDischarged.map(d => d.di))];

    if (graficoDescarregadoResto) graficoDescarregadoResto.destroy();
    if (graficoVolumeCliente) graficoVolumeCliente.destroy();
    if (graficoVolumeDiaPeriodo) graficoVolumeDiaPeriodo.destroy();
    if (graficoVolumeDia) graficoVolumeDia.destroy();
    if (graficoRealizadoClienteDI) graficoRealizadoClienteDI.destroy();
    if (graficoRealizadoPorao) graficoRealizadoPorao.destroy();

    generateFilters('navio', listaNaviosUnicos, filtroNavio);
    generateFilters('periodo', listaPeriodo, filtroPeriodo);
    generateFilters('porao', listaPorao, filtroPorao);
    generateFilters('cliente', listaCliente, filtroCliente);
    generateFilters('armazem', listaArmazem, filtroArmazem);
    generateFilters('produto', listaProduto, filtroProduto);
    generateFilters('di', listaDI, filtroDI);

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
    const dadosDescarregadoResto = filteredDataDischarged.reduce((acc, d) => {
        acc.peso += d.peso;
        return acc;
    }, { peso: 0 });

    const dadosPlanejado = filteredDataPlanned.reduce((acc, d) => {
        acc.planejado += d.planejado;
        return acc;
    }
    , { planejado: 0 });
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
    const dadosRealizadoPorao = filteredDataDischarged.reduce((acc, d) => {
        acc[d.porao] = acc[d.porao] || { peso: 0 };
        acc[d.porao].peso += d.peso;
        return acc;
    }, {});

    const dadosRealizadoPoraoArray = Object.keys(dadosRealizadoPorao).map(porao => ({
        porao: porao,
        peso: dadosRealizadoPorao[porao].peso
    }));

    
    // const dadosRealizadoPorao = await getDischargingData('descarregadoPorao');
    const noDataRealizadoPorao = document.getElementById('emptyGraficoRealizadoPorao');
    const dataGraficoRealizadoPorao = document.getElementById('graficoRealizadoPorao');

    dataGraficoRealizadoPorao.style.visibility = 'hidden';
    noDataRealizadoPorao.style.visibility = 'visible';
    if(dadosRealizadoPoraoArray.length > 0){
        noDataRealizadoPorao.style.visibility = 'hidden';
        dataGraficoRealizadoPorao.style.visibility = 'visible';

        graficoRealizadoPorao = new Chart('graficoRealizadoPorao', {
        type: 'horizontalBar',
        data: {
            labels: dadosRealizadoPoraoArray.map(d => d.porao),
            datasets: [{
                label: 'Realizado',
                data: dadosRealizadoPoraoArray.map(d => ((d.peso / (d.peso + 1000000)) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: 'rgba(82, 183, 136, 0.5)',
                borderColor: 'rgba(82, 183, 136, 0.8)',
                borderWidth: 1
            },
            {
                label: 'Restante',
                data: dadosRealizadoPoraoArray.map(d => ((1 - (d.peso / (d.peso + 1000000)))* 100).toFixed(2)), // Peso descarregado / planejado
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
    // Tratamento dos dados para o uso no gráfico
    const dadosPlanejadoClienteDI = filteredDataPlanned.reduce((acc, d) => {
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`] = acc[`${d.cliente} - ${d.armazem} - ${d.di}`] || { planejado: 0 };
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`].planejado += d.planejado;
        return acc;
    }, {});

    const dadosRealizadoClienteDI = filteredDataDischarged.reduce((acc, d) => {
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`] = acc[`${d.cliente} - ${d.armazem} - ${d.di}`] || { peso: 0};
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`].peso += d.peso;
        return acc;
    }, {});
    
    const mergedDados = {};

    // Merge dadosPlanejadoClienteDI into mergedDados
    Object.keys(dadosPlanejadoClienteDI).forEach(key => {
        mergedDados[key] = { ...dadosPlanejadoClienteDI[key] };
    
        if (dadosRealizadoClienteDI[key]) {
            mergedDados[key] = { ...mergedDados[key], ...dadosRealizadoClienteDI[key] };
        }
    });
    
    // Add missing keys from dadosRealizadoClienteDI to mergedDados
    Object.keys(dadosRealizadoClienteDI).forEach(key => {
        if (!mergedDados[key]) {
            mergedDados[key] = { ...dadosRealizadoClienteDI[key] };
        }
    });

    const mergedDadosArray = Object.entries(mergedDados).map(([key, value]) => {
        const [cliente, armazem, di] = key.split(' - ');
        return { cliente, armazem, di, ...value };
    });

    const noDataGraficoRealizadoClienteDI = document.getElementById('emptyGraficoRealizadoClienteDI');
    const dataGraficoRealizadoClienteDI = document.getElementById('graficoRealizadoClienteDI');

    dataGraficoRealizadoClienteDI.style.visibility = 'hidden';
    noDataGraficoRealizadoClienteDI.style.visibility = 'visible';

    if (mergedDadosArray.length > 0) {
        noDataGraficoRealizadoClienteDI.style.visibility = 'hidden';
        dataGraficoRealizadoClienteDI.style.visibility = 'visible';

    graficoRealizadoClienteDI = new Chart('graficoRealizadoClienteDI', {
        type: 'horizontalBar',
        data: {
            labels: mergedDadosArray.map(d => d.cliente + " - " + d.armazem + " - " + d.di),
            datasets: [{
                label: 'Realizado',
                data: mergedDadosArray.map(d => ((d.peso / d.planejado) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: 'rgba(82, 183, 136, 0.5)',
                borderColor: 'rgba(82, 183, 136, 0.65)',
                borderWidth: 1
            },
            {
                label: 'Restante',
                data: mergedDadosArray.map(d => ((1 - (d.peso / d.planejado))* 100).toFixed(2)), // Peso descarregado / planejado
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
    // const dadosVolumeDia = await getDischargingData('descarregadoDia');
    const dadosVolumeDia = filteredDataDischarged.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { peso: 0 };
        acc[d.data].peso += d.peso;
        return acc;
    }, {});

    const dadosVolumeDiaArray = Object.keys(dadosVolumeDia).map(data => ({
        data: data,
        peso: dadosVolumeDia[data].peso
    }));

    const noDataGraficoVolumeDia = document.getElementById('emptyGraficoVolumeDia');
    const dataGraficoVolumeDia = document.getElementById('graficoVolumeDia');

    dataGraficoVolumeDia.style.visibility = 'hidden';
    noDataGraficoVolumeDia.style.visibility = 'visible';
    if (dadosVolumeDiaArray.length > 0) {
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
            labels: dadosVolumeDiaArray.map(d => d.data),
            datasets: [{
                label: 'Peso',
                data: dadosVolumeDiaArray.map(d => d.peso),
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
    // const dadosVolumeCliente = await getDischargingData('descarregadoCliente');
    const dadosVolumeCliente = filteredDataDischarged.reduce((acc, d) => {
        acc[d.cliente] = acc[d.cliente] || { peso: 0 };
        acc[d.cliente].peso += d.peso;
        return acc;
    }, {});

    const dadosVolumeClienteArray = Object.keys(dadosVolumeCliente).map(cliente => ({
        cliente: cliente,
        peso: dadosVolumeCliente[cliente].peso
    }));

    const noDataGraficoVolumeCliente = document.getElementById('emptyGraficoVolumeCliente');
    const dataGraficoVolumeCliente = document.getElementById('graficoVolumeCliente');

    dataGraficoVolumeCliente.style.visibility = 'hidden';
    noDataGraficoVolumeCliente.style.visibility = 'visible';
    if (dadosVolumeClienteArray.length > 0) {
        noDataGraficoVolumeCliente.style.visibility = 'hidden';
        dataGraficoVolumeCliente.style.visibility = 'visible';

        graficoVolumeCliente = new Chart('graficoVolumeCliente', {
            type: 'horizontalBar',
            data: {
                labels: dadosVolumeClienteArray.map(d => d.cliente),
                datasets: [{
                    label: 'Peso',
                    data: dadosVolumeClienteArray.map(d => d.peso),
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

    const groupedData = filteredDataDischarged.reduce((acc, d) => {
        const key = `${d.data}-${d.periodo}`; // Combine data and periodo into a single key
        if (!acc[key]) {
            acc[key] = { data: d.data, periodo: d.periodo, peso: 0 };
        }
        acc[key].peso += d.peso;
        return acc;
    }, {});
    
    const dataArray = Object.values(groupedData);

    // Group data by 'periodo'
    const dadosAgrupadosDiaPeriodo = dataArray.reduce((acc, d) => {
        acc[d.periodo] = acc[d.periodo] || [];
        acc[d.periodo].push(d);
        return acc;
    }, {});

    // Create a unique set of 'data' values
    const datasUnicas = [...new Set(dataArray.map(d => d.data))];

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
    if (dataArray.length > 0) {
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

async function getUniqueVessels(){
    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: 'uniqueVessels'
        }
    ],
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
            reject(response.error);
        })
    });
}

async function getVesselData($type, $vessel){

    $key = $type == 'discharged' ? 'vesselDataDischarged' : 'vesselDataPlanned';

    var request = {
        url: "shipDischarging/shipDischargingController.php",
        method: 'POST',
        data: [
        {
            name: 'action',
            value: $key
        },
        {
            name: 'navio',
            value: $vessel
        }
    ],
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
            reject(response.error);
        })
    });
}