import { getVesselData, getUniqueVessels } from './balanca_data.js';
import { floatParaFloatFormatado, floatParaStringFormatada, convertSecondsToTime, paralisacoesSoma, renameKeys, getColorForDate, assignColorsToList, colorPalette, pbiThemeColors, pbiThemeColorsBorder  } from '../charts_utils.js';

window.cleanFiltersData = cleanFiltersData;

window.addEventListener("load", async function() {
    // Call the generateFilters function here
    generateCharts();
});

var graficoDescarregadoResto, graficoVolumeCliente, graficoVolumeDiaPeriodo, graficoVolumeDia, graficoRealizadoClienteDI, graficoRealizadoPorao;

var count = 0;

var clienteColorMap;

var vesselName = document.getElementById('nome-navio');

var jaFoiFiltradoNavio = '';
var jaFiltradoPeriodo = [];
var jaFiltradoPorao = [];
var jaFiltradoCliente = [];
var jaFiltradoArmazem = [];
var jaFiltradoProduto = [];
var jaFiltradoDI = [];

var count = 0;

const dataField = document.getElementById('data');

// Ao trocar o valor do filtro de data, os gráficos são alterados com os valores atualizados
dataField.addEventListener('change', async function() {
    await generateCharts();
});

async function generateFilters(campo, filterData, condition){
    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };

    let filteredData = filterData.map(item => ({ 0: item, [campo]: item }));
    const renamedFilteredData = filteredData.map(item => renameKeys(item, keyMapping));

    let multiSelectOptions = {
        data: renamedFilteredData,
        placeholder: 'Todos',
        max: null,
        multiple: true,
        search: true,
        selectAll: true,
        count: true,
        keepOpen: true,
        listAll: false,
        onSelect: async function() {
            await generateCharts();
        },
        onUnselect: async function() {
            await generateCharts();
        }
    } 

    if (condition.includes(campo)) {
        multiSelectOptions['max'] = 1;
        multiSelectOptions['selectAll'] = false;
        multiSelectOptions['listAll'] = false;
    } 

    new MultiSelect(`#lista-${campo}`, 
        multiSelectOptions,
    );
}

async function updateFilters(campo, filterData, alreadySelected){
    if (alreadySelected.length < 1) {
    const listaElement = document.getElementById(`lista-${campo}`);
    const allOptions = listaElement.querySelectorAll('[data-value]'); // Select all options
    
        allOptions.forEach(option => {
            const value = option.getAttribute('data-value');
            const isSelected = option.classList.contains('multi-select-selected'); // Check if the option is already selected
        
            if (!filterData.map(String).includes(value) && !isSelected) {
                // If the option is not in filterData and not already selected, hide it
                option.style.display = 'none';
            } else {
                // Otherwise, ensure it's visible
                option.style.display = 'flex';
            }
        });
    }
}

function cleanFiltersData(){
    [jaFiltradoPeriodo, jaFiltradoPorao, jaFiltradoCliente, jaFiltradoArmazem, jaFiltradoProduto, jaFiltradoDI].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';

    generateCharts();
}

async function gerarGraficoTotalDescarregado(dadosDescarregado, dadosPlanejado) {
    // 1 - Total descarregado e restante
    const dadosDescarregadoResto = dadosDescarregado.reduce((acc, d) => {
        acc.peso += d.peso;
        return acc;
    }, { peso: 0 });

    const dadosPlanejadoAgrupado = dadosPlanejado.reduce((acc, d) => {
        acc.planejado += d.planejado;
        return acc;
    }
    , { planejado: 0 });

    const naoPossuiDados = document.getElementById('emptyGraficoDescarregadoResto');
    const possuiDados = document.getElementById('graficoDescarregadoResto');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    if (dadosDescarregadoResto.peso !== null) {
        
        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

        const dados = {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregadoResto.peso, dadosPlanejadoAgrupado.planejado - dadosDescarregadoResto.peso],
                backgroundColor: [
                    colorPalette['pbiGreenMidHighOpacity'],
                    'rgba(54, 162, 235, 0.05)'
                ],
                borderColor: [
                    colorPalette['softBlue'],
                ],
            }]
        }

        const options = {
            legend: {
                display: false
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const valor_formatado = floatParaFloatFormatado(data.datasets[0].data[tooltipItem.index]);

                        return valor_formatado;
                    }
                }
            },
            cutoutPercentage: 80,
        }

        const doughnutLabel = {
            id: 'doughnutLabel',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const {ctx, data, chartArea} = chart;
        
                // Calculate the center of the chart
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;
                
                const totalDescarregado = data.datasets[0].data[0];
                const totalRestante = data.datasets[0].data[1];
                const totalManifestado = totalDescarregado + totalRestante;
        
                const percentDescarregado = floatParaFloatFormatado(((totalDescarregado / totalManifestado) * 100));
        
                // Set the font properties
                ctx.font = 'bold 1.5vw Arial';
                ctx.fillStyle = 'rgba(61, 68, 101, 0.7)';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle'; // Align vertically in the center
        
                // Draw the text in the center of the chart
                ctx.fillText(percentDescarregado + '%', centerX, centerY);
            }
        }

        graficoDescarregadoResto = new Chart('graficoDescarregadoResto', {
            type: 'doughnut',
            data: dados,
            plugins: [doughnutLabel],
            options: options
        });
    }
}

async function gerarGraficoDescarregadoPorao(dadosDescarregado, dadosPlanejado) {
    const dadosRealizadoPorao = dadosDescarregado.reduce((acc, d) => {
        acc[d.porao] = acc[d.porao] || { peso: 0 };
        acc[d.porao].peso += d.peso;
        return acc;
    }, {});

    const dadosRealizadoPoraoArray = Object.keys(dadosRealizadoPorao).map(porao => ({
        porao: porao,
        peso: dadosRealizadoPorao[porao].peso
    }));

    // const dadosRealizadoPorao = await getDischargingData('descarregadoPorao');
    const naoPossuiDados = document.getElementById('emptyGraficoRealizadoPorao');
    const possuiDados = document.getElementById('graficoRealizadoPorao');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    if(dadosRealizadoPoraoArray.length > 0){

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

        const dados = {
            labels: dadosRealizadoPoraoArray.map(d => d.porao),
            datasets: [{
                label: 'Realizado',
                data: dadosRealizadoPoraoArray.map(d => ((d.peso / (d.peso + 1000000)) * 100).toFixed(2)), // Peso descarregado / planejado
                backgroundColor: colorPalette['pbiGreenMidHighOpacity'],
                borderColor: 'rgba(61, 68, 101, 0.75)',
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
        }

        const options = { 
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
                display: true
            },
            layout: {
                padding: {
                    top: 15,
                    bottom: 15,
                    left: 15,
                    right: 15
                }
            },
            responsive: true,
        }

        graficoRealizadoPorao = new Chart('graficoRealizadoPorao', {
            type: 'horizontalBar',
            data: dados,
            options: options 
        });
    }
}

async function gerarGraficoClienteArmazemDI(dadosDescarregado, dadosPlanejado) {
    // 3 - Realizado por cliente, armazém e DI
    // Tratamento dos dados para o uso no gráfico
    const dadosPlanejadoClienteDI = dadosPlanejado.reduce((acc, d) => {
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`] = acc[`${d.cliente} - ${d.armazem} - ${d.di}`] || { planejado: 0 };
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`].planejado += d.planejado;
        return acc;
    }, {});

    const dadosRealizadoClienteDI = dadosDescarregado.reduce((acc, d) => {
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
    
    const naoPossuiDados = document.getElementById('emptyGraficoRealizadoClienteDI');
    const possuiDados = document.getElementById('graficoRealizadoClienteDI');
    
    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    
    const mergedDadosArrayFiltered = mergedDadosArray.filter(row => "peso" in row);

    const barColorsClienteDI = mergedDadosArrayFiltered.map(d => d.cliente).map(item => ({ item, color: clienteColorMap[item] }))

    const dados = {
        labels: mergedDadosArrayFiltered.map(d => d.cliente + " - " + d.armazem + " - " + d.di),
        datasets: [{
            label: 'Realizado',
            data: mergedDadosArrayFiltered.map(d => ((d.peso / d.planejado) * 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: barColorsClienteDI.map(d => d.color),
            borderColor: 'rgba(61, 68, 101, 0.75)',
            borderWidth: 1
        },
        {
            label: 'Restante',
            data: mergedDadosArrayFiltered.map(d => ((1 - (d.peso / d.planejado))* 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: 'rgba(54, 162, 235, 0.05)',
            borderColor: 'rgba(54, 162, 235, 0.5)',
            borderWidth: 1
        }]
    }

    const options = {
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
            layout: {
                padding: {
                    top: 15,
                    bottom: 15,
                    left: 15,
                    right: 15
                }
            },
            plugins: {
                datalabels: {
                    display: (value, context) => {
                        return value.datasetIndex === 0; // Só exibe o valor para o dataset de 'Realizado'
                    },
                    color: 'black',
                    anchor: 'center',
                    align: (value, context) => {
                        return value.dataset.data[value.dataIndex] > 90 ? 'start' : 'end'
                    },
                    offset: 3,
                    formatter: (value, context) => {
                        return floatParaFloatFormatado(value, 0) + '%';
                    }
                },
            },
            responsive: true, 
    }
    if (mergedDadosArrayFiltered.length > 0) {
        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

    graficoRealizadoClienteDI = new Chart('graficoRealizadoClienteDI', {
        type: 'horizontalBar',
        plugins: [ChartDataLabels],
        data: dados,
        options: options
        });
    }
}

async function gerarGraficoVolumePorDia(dadosDescarregado) {
    // 4 - Volume descarregado por dia
    // const dadosVolumeDia = await getDischargingData('descarregadoDia');
    const dadosVolumeDia = dadosDescarregado.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { peso: 0 };
        acc[d.data].peso += d.peso;
        return acc;
    }, {});

    const dadosVolumeDiaArray = Object.keys(dadosVolumeDia).map(data => ({
        data: data,
        peso: dadosVolumeDia[data].peso
    }));

    const naoPossuiDados = document.getElementById('emptyGraficoVolumeDia');
    const possuiDados = document.getElementById('graficoVolumeDia');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    const ctx = possuiDados.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 1)");

    var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
    gradientFill.addColorStop(1, "rgba(128, 182, 244, 0.3)");
    gradientFill.addColorStop(0, "rgba(61, 68, 101, 0.3)");

    const dados = {
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
    }

    const options = {
        scales: {
            yAxes: [{
                ticks: {
                    callback: function(value, index, values) {
                        return floatParaStringFormatada(value);
                    },
                    beginAtZero: true
                },
                gridLines: {
                    display: true,
                    drawBorder: true
                },
                display: true
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
        maintainAspectRatio: true,
        layout: {
            padding: {
                top: 15,
                bottom: 15,
                left: 15,
                right: 15
            }
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    const valor_formatado = floatParaFloatFormatado(data.datasets[0].data[tooltipItem.index]);

                    return valor_formatado;
                }
            }
        },
    }

    if (dadosVolumeDiaArray.length > 0) {

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';
    

    graficoVolumeDia = new Chart('graficoVolumeDia', {
        type: 'line',
        data: dados,
        options: options
    });
    }
}

async function gerarGraficoVolumePorCliente(dadosDescarregado) {
    // 5 - Volume descarregado por cliente
    const dadosVolumeCliente = dadosDescarregado.reduce((acc, d) => {
        acc[d.cliente] = acc[d.cliente] || { peso: 0 };
        acc[d.cliente].peso += d.peso;
        return acc;
    }, {});

    const dadosVolumeClienteArray = Object.keys(dadosVolumeCliente).map(cliente => ({
        cliente: cliente,
        peso: dadosVolumeCliente[cliente].peso
    }));

    const naoPossuiDados = document.getElementById('emptyGraficoVolumeCliente');
    const possuiDados = document.getElementById('graficoVolumeCliente');

    let dadosClienteOrdenados = [];
    // Convert the object to an array of [cliente, {peso: value}] pairs
    const sortedArray = Object.entries(dadosVolumeClienteArray).sort((a, b) => {
        return b[1].peso - a[1].peso;
    });

    sortedArray.forEach((item) => {
        dadosClienteOrdenados.push(item[1])
    });

    const clientesUnicos = [...new Set(dadosClienteOrdenados.map(d => d.cliente))];

    const barColorCliente = clientesUnicos.map(item => ({ item, color: clienteColorMap[item] }))
    
    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    const dados = {
        labels: dadosClienteOrdenados.map(d => d.cliente),
        datasets: [{
            label: 'Peso',
            data: dadosClienteOrdenados.map(d => d.peso),
            backgroundColor: barColorCliente.map(d => d.color),
            borderColor: 'rgba(61, 68, 101, 0.75)',
            borderWidth: 1
        }]
    }

    const options = {
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
        maintainAspectRatio: true,
        layout: {
            padding: {
                top: 15,
                bottom: 15,
                left: 15,
                right: 70
            }
        },
        plugins: {
            datalabels: {
                display: true,
                borderRadius: 5,
                padding: 10,
                color: 'black',
                anchor: 'start',
                align: 'end',
                offset: 0,
                formatter: (value, context) => {
                    return floatParaStringFormatada(value);
                }
            },
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    const valor_formatado = floatParaFloatFormatado(data.datasets[0].data[tooltipItem.index]);

                    return valor_formatado;
                }
            }
        },
    }
    if (dadosVolumeClienteArray.length > 0) {
        
        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

        graficoVolumeCliente = new Chart('graficoVolumeCliente', {
            type: 'horizontalBar',
            plugins: [ChartDataLabels],
            data: dados,
            options: options
        });
    }
}

async function gerarGraficoVolumeDiaPeriodo(dadosDescarregado) {
    const groupedData = dadosDescarregado.reduce((acc, d) => {
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
            borderColor: 'rgba(61, 68, 101, 0.75)',
            borderWidth: 1
        };
    });

    // 6 - Volume descarregado por dia e período
    const naoPossuiDados = document.getElementById('emptyGraficoVolumeDiaPeriodo');
    const possuiDados = document.getElementById('graficoVolumeDiaPeriodo');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    const dados = {
        labels: datasUnicas,
        datasets: datasets
    }

    const options = {
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
            display: true
        },
        layout: {
            padding: {
                top: 15,
                bottom: 15,
                left: 15,
                right: 15
            }
        },
        scales: {
            xAxes: [{
                display: true,
                gridLines: {
                    display: true
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    display: true
                },
                ticks: {
                    callback: function(value, index, values) {
                        return floatParaStringFormatada(value);
                    },
                }
            }],
        },
        responsive: true,
        maintainAspectRatio: true,
    }

    if (dataArray.length > 0) {

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

        graficoVolumeDiaPeriodo = new Chart('graficoVolumeDiaPeriodo', {
            type: 'bar',
            data: dados,
            options: options
        });
    }
}

async function generateCharts() {
    const listaNavio = await getUniqueVessels();

    // Map through listaNavio, convert each object's values to a Set to remove duplicates, then convert back to array
    const arrayNaviosUnicos = listaNavio.map(obj => [...new Set(Object.values(obj))]);
    
    // Flatten the array of arrays to get a single array with all values
    const listaNaviosUnicos = arrayNaviosUnicos.flat();

    let filtroData = document.getElementById('data').value === '' ? null : [document.getElementById('data').value];

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPorao = Array.from(document.getElementById('lista-porao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroCliente = Array.from(document.getElementById('lista-cliente').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroArmazem = Array.from(document.getElementById('lista-armazem').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroProduto = Array.from(document.getElementById('lista-produto').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroDI = Array.from(document.getElementById('lista-di').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)

    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoPorao = filtroPorao;
    jaFiltradoCliente = filtroCliente;
    jaFiltradoArmazem = filtroArmazem;
    jaFiltradoProduto = filtroProduto;
    jaFiltradoDI = filtroDI;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].navio;

    const dataDischarged = await getVesselData('discharged', navioSelecionado);

    if (navioSelecionado !== jaFoiFiltradoNavio && count > 1) {
        filtroData = null;
        document.getElementById('data').value = ''
        jaFiltradoArmazem = [];
        jaFiltradoCliente = [];
        jaFiltradoDI = [];
        jaFiltradoPeriodo = [];
        jaFiltradoPorao = [];
        jaFiltradoProduto = [];
    }

    vesselName.innerText = navioSelecionado;

    const formattedDataDischarged = dataDischarged.map(item => {
        if (item.data) {
            // Split the date string by space and take the first part (date)
            const formattedDate = item.data.split(' ')[0];

            // Return a new object with the formatted date
            return { ...item, data: formattedDate };
        }
    });

    const dataPlanned = await getVesselData('planned', navioSelecionado);

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataDischarged = formattedDataDischarged.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = jaFiltradoPeriodo.length === 0 || jaFiltradoPeriodo.includes(`'${item.periodo}'`);
        const matchesPorao = jaFiltradoPorao.length === 0 || jaFiltradoPorao.includes(`'${item.porao}'`);
        const matchesCliente = jaFiltradoCliente.length === 0 || jaFiltradoCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = jaFiltradoArmazem.length === 0 || jaFiltradoArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = jaFiltradoProduto.length === 0 || jaFiltradoProduto.includes(`'${item.produto}'`);
        const matchesDI = jaFiltradoDI.length === 0 || jaFiltradoDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesData && matchesPeriodo && matchesPorao && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataPlanned = dataPlanned.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesCliente = jaFiltradoCliente.length === 0 || jaFiltradoCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = jaFiltradoArmazem.length === 0 || jaFiltradoArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = jaFiltradoProduto.length === 0 || jaFiltradoProduto.includes(`'${item.produto}'`);
        const matchesDI = jaFiltradoDI.length === 0 || jaFiltradoDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    const clientesUnicos = [...new Set(filteredDataDischarged.map(d => d.cliente))];
    if (count < 1) clienteColorMap = assignColorsToList(clientesUnicos, pbiThemeColors);
    
    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))].sort();
    const listaPorao = [...new Set(filteredDataDischarged.map(d => d.porao))].sort();
    const listaCliente = [...new Set(filteredDataDischarged.map(d => d.cliente))].sort();
    const listaArmazem = [...new Set(filteredDataDischarged.map(d => d.armazem))].sort();
    const listaProduto = [...new Set(filteredDataDischarged.map(d => d.produto))].sort();
    const listaDI = [...new Set(filteredDataDischarged.map(d => d.di))].sort();

    if (graficoDescarregadoResto) graficoDescarregadoResto.destroy();
    if (graficoVolumeCliente) graficoVolumeCliente.destroy();
    if (graficoVolumeDiaPeriodo) graficoVolumeDiaPeriodo.destroy();
    if (graficoVolumeDia) graficoVolumeDia.destroy();
    if (graficoRealizadoClienteDI) graficoRealizadoClienteDI.destroy();
    if (graficoRealizadoPorao) graficoRealizadoPorao.destroy();
    
    if (count < 1 || jaFoiFiltradoNavio !== navioSelecionado) {
        if (count < 1) generateFilters('navio', listaNaviosUnicos, ['navio']);
        generateFilters('periodo', listaPeriodo, ['navio']);
        generateFilters('porao', listaPorao, ['navio']);
        generateFilters('cliente', listaCliente, ['navio']);
        generateFilters('armazem', listaArmazem, ['navio']);
        generateFilters('produto', listaProduto, ['navio']);
        generateFilters('di', listaDI, ['navio']);
    } else {
        updateFilters('periodo', listaPeriodo, jaFiltradoPeriodo);
        updateFilters('porao', listaPorao, jaFiltradoPorao);
        updateFilters('cliente', listaCliente, jaFiltradoCliente);
        updateFilters('armazem', listaArmazem, jaFiltradoArmazem);
        updateFilters('produto', listaProduto, jaFiltradoProduto);
        updateFilters('di', listaDI, jaFiltradoDI);
    }
    
    jaFoiFiltradoNavio = navioSelecionado;
    count++;
    
    await gerarGraficoTotalDescarregado(filteredDataDischarged, dataPlanned);

    await gerarGraficoDescarregadoPorao(filteredDataDischarged, filteredDataPlanned);
    
    await gerarGraficoClienteArmazemDI(filteredDataDischarged, filteredDataPlanned);
    
    await gerarGraficoVolumePorDia(filteredDataDischarged)

    await gerarGraficoVolumePorCliente(filteredDataDischarged);
    
    await gerarGraficoVolumeDiaPeriodo(filteredDataDischarged)
    }