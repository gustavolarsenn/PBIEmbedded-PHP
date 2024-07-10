import { getVesselInfo, getVesselData, getUniqueVessels } from './prancha_data.js';

window.cleanFiltersData = cleanFiltersData;

document.addEventListener('DOMContentLoaded', function () {
    generateCharts();
});

var colorPalette = {
    'sideBarColor': 'rgba(61, 68, 101, 0.6)', // Cor do sidebar (app)
    'pbiGreenMidHighOpacity': 'rgba(55, 167, 148, 0.65)', // Fundo verde (PBI Port Statistics)
    'pbiGreenMidLowOpacity': 'rgba(55, 167, 148, 0.5)', // Fundo verde (PBI Port Statistics)
    'pbiGreenFull': 'rgba(55, 167, 148, 1)', // Borda verde (PBI Port Statistics)
    'softBlue': 'rgba(54, 162, 235, 0.05)', // Donut de resto
    'coolBlue': 'rgba(144, 200, 255, 0.8)'
}

const pbiThemeColors = ["rgba(50, 87, 168, 0.65)","rgba(55, 167, 148, 0.65) ","rgba(139, 61, 136, 0.65)",
    "rgba(221, 107, 127, 0.65)","rgba(107, 145, 201, 0.65)","rgba(245, 200, 105, 0.65)","rgba(119, 196, 168, 0.65)",
    "rgba(222, 166, 207, 0.65)","rgba(186, 74, 197, 0.65)",
    "rgba(197, 74, 83, 0.65)","rgba(254, 226, 102, 0.65)","rgba(62, 155, 128, 0.65)",
    "rgba(197, 74, 145, 0.65)","rgba(37, 69, 181, 0.65)","rgba(128, 22, 137, 0.65)",
    "rgba(137, 22, 30, 0.65)","rgba(22, 31, 137, 0.65)","rgba(4, 114, 87, 0.65)","rgba(137, 22, 88, 0.65)","rgba(24, 45, 121, 0.65)","rgba(15, 21, 92, 0.65)"]

const pbiThemeColorsBorder = ["rgba(50, 87, 168, 1)","rgba(55, 167, 148, 1) ","rgba(139, 61, 136, 1)",
    "rgba(221, 107, 127, 1)","rgba(107, 145, 201, 1)","rgba(245, 200, 105, 1)","rgba(119, 196, 168, 1)",
    "rgba(222, 166, 207, 1)","rgba(186, 74, 197, 1)",
    "rgba(197, 74, 83, 1)","rgba(254, 226, 102, 1)","rgba(62, 155, 128, 1)",
    "rgba(197, 74, 145, 1)","rgba(37, 69, 181, 1)","rgba(128, 22, 137, 1)",
    "rgba(137, 22, 30, 1)","rgba(22, 31, 137, 1)","rgba(4, 114, 87, 1)","rgba(137, 22, 88, 1)","rgba(24, 45, 121, 1)","rgba(15, 21, 92, 1)"]

// Step 1: Define a function to determine the color based on the date
const shuffledColors = pbiThemeColors.sort(() => 0.5 - Math.random()); // Shuffle the colors array
const shuffledColorsBorder = pbiThemeColorsBorder.sort(() => 0.5 - Math.random()); // Shuffle the colors array

var graficoTotalDescarregado, graficoDescarregadoDia, graficoResumoGeral, graficoTempoParalisado, graficoDescarregadoDiaPeriodo;

var graficoResumoGeral;

var count = 0;

let filtrosParalisacao = {
    'chuva': 'Chuva',
    'forca_maior': 'Força Maior',
    'transporte': 'Transporte',
    'outros': 'Outros'
};

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


let firstBarChartOptions = JSON.parse(JSON.stringify(barOptions)); // Deep copy
let secondBarChartOptions = JSON.parse(JSON.stringify(barOptions)); // Deep copy
secondBarChartOptions.legend.display = true;

var vesselName = document.getElementById('vessel-name');

var tagGraficoDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');
var tagGraficoDiaPeriodoContainer = document.getElementById('graficoDescarregadoDiaPeriodoContainer');

var infoVesselTag = document.getElementById('info-vessel');
var infoBerthTag = document.getElementById('info-berth');
var infoProductTag = document.getElementById('info-product');
var infoModalityTag = document.getElementById('info-modality');
var infoVolumeTag = document.getElementById('info-volume');
var infoDateTag = document.getElementById('info-date');
var infoMinimumDischargeTag = document.getElementById('info-minimum-discharge');

var infoPranchaAferida = document.getElementById('prancha-aferida');
var infoMetaAlcancada = document.getElementById('meta-alcancada');

var infoDescarregado = document.getElementById('info-descarregado');
var infoRestante = document.getElementById('info-restante');
var paralisacaoSelecionada = document.getElementById('paralisacao-selecionada');

var jaFoiFiltradoNavio = '';
var jaFiltradoRelatorio = [];
var jaFiltradoPeriodo = [];
var jaFiltradoParalisacao = [];

var count = 0;

function getColorForDate(date, colorTheme) {
    // Assuming `date` is a Date object. Adjust the logic if it's a string or another format.

    const dateDate = new Date(date);
    const dayOfWeek = dateDate.getDate(); // getDay() returns 0 for Sunday, 1 for Monday, etc.

    // Let's say weekends are red, weekdays are green
    return colorTheme[dayOfWeek % pbiThemeColors.length];
}

const convertSecondsToTime = (seconds) => {
     // Convert total time from seconds to hh:mm:ss format
     const hours = Math.floor(seconds / 3600);
     const minutes = Math.floor((seconds % 3600) / 60);
 
     // Format the time string, ensuring two digits for hours, minutes, and seconds
     const value_time = [hours, minutes]
         .map(val => val < 10 ? `0${val}` : val)
         .join('h ');
    return value_time + 'm';
}

const paralisacoesSoma = (filteredValues, filteredData, possibleFilters) => {
    const filteredColumnsOnly = filteredData.map(item => {
        const filteredKeys = filteredValues.map(item => Object.keys(possibleFilters).find(key => possibleFilters[key] === item.slice(1, -1)))
        return Object.keys(item).filter(key => filteredKeys.includes(key)).reduce((acc, key) => {
            acc[key] = item[key];
            return acc;
        }, {});
    });
    const somaTempoParalisado = filteredColumnsOnly.reduce((acc, d) => {
        acc.total += Number(d.chuva) || 0;
        acc.total += Number(d.forca_maior) || 0;
        acc.total += Number(d.transporte) || 0;
        acc.total += Number(d.outros) || 0;
    
        return acc;
    }, { total: 0, chuva: 0, forca_maior: 0, transporte: 0, outros: 0 });

    return somaTempoParalisado.total
} 


const dataField = document.getElementById('data');

// Ao trocar o valor do filtro de data, os gráficos são alterados com os valores atualizados
dataField.addEventListener('change', async function() {
    await generateCharts();
});

function renameKeys(obj, keyMap) {
    return Object.keys(obj).reduce((acc, key) => {
        const newKey = keyMap[key] || key; // Use new key name if it exists in the mapping, otherwise use the original key
        acc[newKey] = obj[key]; // Assign the value to the new key in the accumulator object
        return acc;
    }, {}); // Initial value for the accumulator is an empty object
}

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
        multiSelectOptions['multiple'] = false;
        multiSelectOptions['selectAll'] = false;
    } 

    new MultiSelect(`#lista-${campo}`, 
        multiSelectOptions,
    );
}

async function updateFilters(campo, filterData, alreadySelected){
    if (alreadySelected.length < 1) {
    paralisacaoSelecionada.innerText = '';
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
    [jaFiltradoPeriodo, jaFiltradoRelatorio, jaFiltradoParalisacao].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';
    paralisacaoSelecionada.innerHTML = '';

    generateCharts();
}

async function gerarGraficoTotalDescarregado(valor_descarregado, valor_manifestado) {
    // 1 - Total descarregado e restante
    const noDataGraficoDescarregadoResto = document.getElementById('emptyGraficoTotalDescarregado');
    const dataGraficoDescarregadoResto = document.getElementById('graficoTotalDescarregado');

    infoDescarregado.innerText = valor_descarregado.toFixed(2);
    infoRestante.innerText = (valor_manifestado - valor_descarregado).toFixed(2);

    dataGraficoDescarregadoResto.style.visibility = 'hidden';
    noDataGraficoDescarregadoResto.style.visibility = 'visible';
    noDataGraficoDescarregadoResto.style.display = 'flex';
    if (valor_descarregado !== null) {
        noDataGraficoDescarregadoResto.style.visibility = 'hidden';
        dataGraficoDescarregadoResto.style.visibility = 'visible';
        noDataGraficoDescarregadoResto.style.display = 'none';


        const totalDescarregadoData = {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [valor_descarregado, valor_manifestado - valor_descarregado],
                backgroundColor: [
                    colorPalette['pbiGreenMidHighOpacity'],
                    colorPalette['softBlue']
                ],
                borderColor: [
                    colorPalette['pbiGreenFull'],
                ],
            }]
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

                const percentDescarregado = ((totalDescarregado / totalManifestado) * 100).toFixed(2);
                // // Calculate the total value of all data points
                // const totalValue = data.datasets[0].data.reduce((acc, value) => acc + value, 0);
        
                // Set the font properties
                ctx.font = 'bold 1.5rem Arial';
                ctx.fillStyle = 'rgba(61, 68, 101, 0.8)';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle'; // Align vertically in the center
        
                // Draw the text in the center of the chart
                ctx.fillText(percentDescarregado + '%', centerX, centerY);
            }
        };

        const totalDescarregadoOptions = {
            plugins: {
                doughnutLabel: doughnutLabel
            },
            legend: {
                display: false
            },
            cutoutPercentage: 75,
            responsive: true,
            maintainAspectRatio: true,
        }

        graficoTotalDescarregado = new Chart('graficoTotalDescarregado', {
        type: 'doughnut',
        data: totalDescarregadoData,
        options: totalDescarregadoOptions,
        plugins: [doughnutLabel],
    });
    }
}

async function gerarGraficoDescarregadoPorDia(dataDischarged) {
    // 4 - Volume descarregado por dia
    const dadosVolumeDia = dataDischarged.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { volume: 0 };
        acc[d.data].volume += d.volume;
        return acc;
    }, {});

    const dadosVolumeDiaArray = Object.keys(dadosVolumeDia).map(data => ({
        data: data,
        volume: dadosVolumeDia[data].volume
    }));

    const dadosMetaDia = dataDischarged.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { meta: 0 };
        acc[d.data].meta += d.meta;
        return acc;
    }, {});

    const dadosMetaDiaArray = Object.keys(dadosMetaDia).map(data => ({
        data: data,
        meta: dadosMetaDia[data].meta
    }));

    const noDataGraficoVolumeDia = document.getElementById('emptyGraficoDescarregadoDia');
    const dataGraficoVolumeDia = document.getElementById('graficoDescarregadoDia');

    dataGraficoVolumeDia.style.visibility = 'hidden';
    noDataGraficoVolumeDia.style.visibility = 'visible';
    noDataGraficoVolumeDia.style.display = 'block';
    if (dadosVolumeDiaArray.length > 0) {
        noDataGraficoVolumeDia.style.visibility = 'hidden';
        noDataGraficoVolumeDia.style.display = 'none';
        dataGraficoVolumeDia.style.visibility = 'visible';
        
    
    const ctx = dataGraficoVolumeDia.getContext('2d');

    const volumePorDiaColors = dadosVolumeDiaArray.map(d => getColorForDate(d.data, shuffledColors));
    const volumePorDiaColorsBorder = dadosVolumeDiaArray.map(d => getColorForDate(d.data, shuffledColorsBorder));

    graficoDescarregadoDia = new Chart('graficoDescarregadoDia', {
        type: 'bar',
        data: {
            labels: dadosVolumeDiaArray.map(d => d.data),
            datasets: [
                {
                    label: 'Meta',
                    data: dadosMetaDiaArray.map(d => d.meta),
                    backgroundColor: 'rgba(0, 0, 0, 0)',
                    borderColor: 'rgba(61, 68, 101, 0.8)',
                    borderWidth: 4,
                    type: 'line',
                    lineTension: 0,
                },
                {
                label: 'Volume',
                data: dadosVolumeDiaArray.map(d => d.volume),
                backgroundColor: volumePorDiaColors,
                borderColor: volumePorDiaColorsBorder,
                pointBorderWidth: 10,
                pointHoverRadius: 10,
                pointHoverBorderWidth: 1,
                pointRadius: 2,
                fill: true,
                borderWidth: 1,
            },

        ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    display: true
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    barPercentage: 0.75,
                }]
            },
            legend: {
                display: true
            },
            layout: {
                padding: {
                    top: 5,
                }
        },
            responsive: true,
            maintainAspectRatio: true,
        }
    });
    }
}

async function gerarGraficoResumoGeral(dataDischarged) {
    const categories = ['chuva', 'forca_maior', 'transporte', 'duracao', 'horas_operacionais'];

    const reducedData = categories.reduce((acc, category) => {
        // Sum the values for the current category
        acc[category] = dataDischarged.reduce((sum, d) => sum + d[category], 0);
    
        // Sum the time values for the current category, assuming it's in seconds
        const totalTimeInSeconds = dataDischarged.reduce((sum, d) => sum + d[`${category}`], 0);
    
        // Convert total time from seconds to hh:mm:ss format
        const hours = Math.floor(totalTimeInSeconds / 3600);
        const minutes = Math.floor((totalTimeInSeconds % 3600) / 60);
        const seconds = totalTimeInSeconds % 60;
    
        // Format the time string, ensuring two digits for hours, minutes, and seconds
        acc[`${category}_time`] = [hours, minutes, seconds]
            .map(val => val < 10 ? `0${val}` : val)
            .join(':');
    
        return acc;
    }, {});

    const noDataGraficoResumoGeral = document.getElementById('emptyGraficoResumoGeral');
    const dataGraficoResumoGeral = document.getElementById('graficoResumoGeral');

    const ctx = dataGraficoResumoGeral.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 300, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

    dataGraficoResumoGeral.style.visibility = 'hidden';
    noDataGraficoResumoGeral.style.visibility = 'visible';
    noDataGraficoResumoGeral.style.display = 'block';

    if (dataDischarged !== null) {
        noDataGraficoResumoGeral.style.visibility = 'hidden';
        dataGraficoResumoGeral.style.visibility = 'visible';
        noDataGraficoResumoGeral.style.display = 'none';
        const colors = {
            'Chuva': 'rgba(144, 215, 255, 0.5)',
            'Força Maior': 'rgba(191, 208, 224, 0.5)',
            'Transporte': 'rgba(93, 253, 203, 0.5)',
            'Duração': 'rgba(8, 76, 97, 0.5)',
            'Horas operacionais': 'rgba(6, 214, 160, 0.5)',
        },

        graficoResumoGeral = new Chart('graficoResumoGeral', {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                // labels: Object.keys(reducedData).filter(key => categories.includes(key)).map(key => key.replace('_', ' ')),
                labels: ['Chuva', 'Força Maior', 'Transporte', 'Duração', ['Horas', 'operacionais']],
                datasets: [
                    {
                        label: 'Tempo',
                        data: Object.keys(reducedData).filter(key => categories.includes(key)).map(key => reducedData[key]),
                        backgroundColor: Object.keys(colors).map(key => colors[key]),
                        borderColor: 'rgba(61, 68, 101, 0.8)',
                        borderWidth: 1,
                    }
                ],
            },
            options: {
                ...barOptions,
                scales: {
                    xAxes: [{
                        display: true,
                    }],
                    yAxes: [{
                        display: false,
                    }],
                },
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false,
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const value_time = convertSecondsToTime(tooltipItem.yLabel);

                            return value_time;
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        display: true,
                        borderRadius: 5,
                        padding: 4,
                        color: 'black',
                        anchor: 'start',
                        align: 'top',
                        offset: 0,
                        formatter: (value, context) => {
                            const value_time = convertSecondsToTime(value);
                        return value_time;
                        }
                    },
                },
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15,
                        left: 15,
                        right: 15
                    },
                },
            },
        });
    }
}

async function gerarGraficoTempoParalisado(dataDischarged) {

    const categories = ['chuva', 'forca_maior', 'transporte', 'duracao', 'horas_operacionais'];

    const dates = [...new Set(dataDischarged.map(d => d.data))];

    const noDataGraficoResumoGeral = document.getElementById('emptyGraficoTempoParalisado');
    const dataGraficoResumoGeral = document.getElementById('graficoTempoParalisado');

    const ctx = dataGraficoResumoGeral.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 300, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

    dataGraficoResumoGeral.style.visibility = 'hidden';
    noDataGraficoResumoGeral.style.visibility = 'visible';
    noDataGraficoResumoGeral.style.display = 'block';

    if (dataDischarged !== null) {
        noDataGraficoResumoGeral.style.visibility = 'hidden';
        dataGraficoResumoGeral.style.visibility = 'visible';
        noDataGraficoResumoGeral.style.display = 'none';

        function aggregateDataByDate(data, date, category) {
            return data.filter(d => d.data === date).map(d => d[category]);
        }
        
        // Create datasets for each category
        const datasets = [
            {
                label: 'Chuva',
                data: dates.map(date => aggregateDataByDate(dataDischarged, date, 'chuva').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(144, 215, 255, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Transporte',
                data: dates.map(date => aggregateDataByDate(dataDischarged, date, 'transporte').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(93, 253, 203, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Força maior',
                data: dates.map(date => aggregateDataByDate(dataDischarged, date, 'forca_maior').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(191, 208, 224, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Outros',
                data: dates.map(date => aggregateDataByDate(dataDischarged, date, 'outros').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(184, 179, 190, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
        ];
        
        // Create the chart
        graficoTempoParalisado = new Chart('graficoTempoParalisado', {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: dates,
                datasets: datasets,
            },
            options: {
                ...barOptions,
                legend: {
                    display: true
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        gridLines: {
                            display: true
                        },
                        barPercentage: 0.75,
                    }],
                    yAxes: [{
                        stacked: true,
                        display: false,
                    }],
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const value_time = convertSecondsToTime(tooltipItem.yLabel);
                            return value_time;
                        }
                    }
                },
                plugins: {
                    datalabels: {
                        display: true,
                        borderRadius: 5,
                        padding: 4,
                        color: 'black',
                        anchor: 'start',
                        align: 'top',
                        offset: 0,
                        formatter: (value, context) => {
                            // Identify if the current dataset is the last one in its stack
                            const datasets = context.chart.data.datasets;
                            const currentStack = context.dataset.stack;
                            const currentIndex = context.dataIndex;
                            const currentDatasetIndex = datasets.indexOf(context.dataset);
                            let isLastDatasetInStack = true; // Assume it is the last dataset initially
                        
                            // Check if any subsequent dataset belongs to the same stack and has data for the current index
                            for (let i = currentDatasetIndex + 1; i < datasets.length; i++) {
                                if (datasets[i].stack === currentStack && datasets[i].data[currentIndex] != null) {
                                    isLastDatasetInStack = false; // Found a dataset in the same stack that comes after the current one
                                    break;
                                }
                            }
                        
                            // If it's the last dataset in the stack, calculate and display the sum
                            if (isLastDatasetInStack) {
                                const sum = datasets.reduce((acc, dataset) => {
                                    if (dataset.stack === currentStack) {
                                        const datasetValue = dataset.data[currentIndex] || 0;
                                        return acc + datasetValue;
                                    }
                                    return acc;
                                }, 0);
                        
                                if (sum > 0) {
                                    const value_time = convertSecondsToTime(sum); // Assuming convertSecondsToTime is a function you've defined
                                    return value_time;
                                }
                            }
                            return ''; // For datasets that are not the last in the stack, don't display a label
                        }
                    },
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
                }
            },
        });
    }
}

async function gerarGraficoDescarregadoDiaPeriodo(dataDischarged) {
    const noDataGraficoVolumeDiaPeriodo = document.getElementById('emptyGraficoDescarregadoDiaPeriodo');
    const dataGraficoVolumeDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');

    dataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
    noDataGraficoVolumeDiaPeriodo.style.visibility = 'visible';
    noDataGraficoVolumeDiaPeriodo.style.display = 'block';
    if (dataDischarged.length > 0) {
        noDataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
        noDataGraficoVolumeDiaPeriodo.style.display = 'none';
        dataGraficoVolumeDiaPeriodo.style.visibility = 'visible';

        const combinedLabelsDisplay = dataDischarged.map(item => [`${item.data}`, `${item.periodo}`]);
        const uniqueCombinedLabelsDisplay = [...new Set(combinedLabelsDisplay)];
        
// Step 2: Apply the function to your data to generate an array of colors
// Assuming `dataDischarged` contains objects with a `date` property that is a Date object
const barColors = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColors));
const barColorsBorder = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColorsBorder));

// Step 3: Assign the generated colors to `backgroundColor` in your dataset
graficoDescarregadoDiaPeriodo = new Chart('graficoDescarregadoDiaPeriodo', {
    type: 'bar',
    data: {
        labels: uniqueCombinedLabelsDisplay,
        datasets: [
            {
                label: 'Meta',
                data: dataDischarged.map(d => d.meta),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgba(61, 68, 101, 0.8)',
                borderWidth: 4,
                type: 'line',
                lineTension: 0,
            },
            {
                label: 'Volume',
                data: dataDischarged.map(d => d.volume),
                backgroundColor: barColors, // Use the generated array of colors
                borderColor: barColorsBorder,
                borderWidth: 1,
            },
        ]
    },
    options: {
        legend: {
            display: false
        },
        scales: {
            xAxes: [{
                position: 'bottom',
                gridLines: {
                    display: false
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    display: true
                }
            }]
        },
        maintainAspectRatio: true,
    }
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
    const filtroRelatorio = Array.from(document.getElementById('lista-relatorio_no').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroMotivoParalisacao = Array.from(document.getElementById('lista-motivo_paralisacao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)

    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoRelatorio = filtroRelatorio;
    jaFiltradoParalisacao = filtroMotivoParalisacao;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].navio;

    const dataDischarged = await getVesselData(navioSelecionado);

    const vesselData = await getVesselInfo(navioSelecionado);



    if (navioSelecionado !== jaFoiFiltradoNavio && count > 1) {
        filtroData = null;
        document.getElementById('data').value = ''
        jaFiltradoPeriodo = [];
        jaFiltradoRelatorio = [];
        jaFiltradoParalisacao = [];
        paralisacaoSelecionada.innerHTML = '';
    }

    infoVesselTag.innerText = vesselData[0].navio;
    infoBerthTag.innerText = vesselData[0].berco;
    infoProductTag.innerText = vesselData[0].produto;
    infoModalityTag.innerText = vesselData[0].modalidade;
    infoVolumeTag.innerText = vesselData[0].volume_manifestado;
    infoDateTag.innerText = vesselData[0].data.split(' ')[0];
    infoMinimumDischargeTag.innerText = vesselData[0].prancha_minima;

    const formattedDataDischarged = dataDischarged.map(item => {
        if (item.data) {
            // Split the date string by space and take the first part (date)
            const formattedDate = item.data.split(' ')[0];

            // Return a new object with the formatted date
            return { ...item, data: formattedDate };
        }
    });

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataDischarged = formattedDataDischarged.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = jaFiltradoPeriodo.length === 0 || jaFiltradoPeriodo.includes(`'${item.periodo}'`);
        const matchesRelatorio = jaFiltradoRelatorio.length === 0 || jaFiltradoRelatorio.includes(`'${item.relatorio_no}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesData && matchesPeriodo && matchesRelatorio;
    });

    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))].sort();
    const listaRelatorio = [...new Set(filteredDataDischarged.map(d => d.relatorio_no))].sort();
    
    if (graficoTotalDescarregado) graficoTotalDescarregado.destroy();
    if (graficoDescarregadoDia) graficoDescarregadoDia.destroy();
    if (graficoResumoGeral) graficoResumoGeral.destroy();
    if (graficoTempoParalisado) graficoTempoParalisado.destroy();
    if (graficoDescarregadoDiaPeriodo) graficoDescarregadoDiaPeriodo.destroy();
    
    if (count < 1 || jaFoiFiltradoNavio !== navioSelecionado) {
        if (count < 1) generateFilters('navio', listaNaviosUnicos, ['navio']);
        generateFilters('periodo', listaPeriodo, ['navio']);
        generateFilters('relatorio_no', listaRelatorio, ['navio']);
        generateFilters('motivo_paralisacao', Object.values(filtrosParalisacao), ['navio']);
    } else {
        // updateFilters('motivo_paralisacao', Object.values(filtrosParalisacao), jaFiltradoParalisacao);
        updateFilters('periodo', listaPeriodo, jaFiltradoPeriodo);
        updateFilters('relatorio_no', listaRelatorio, jaFiltradoRelatorio);
    }
    
    jaFoiFiltradoNavio = navioSelecionado;
    count++;

    const dadosDescarregado = filteredDataDischarged.reduce((acc, d) => {
        acc.volume += d.volume;
        
        return acc;
    }, { volume: 0});

    const somaTempoParalisado = paralisacoesSoma(jaFiltradoParalisacao, formattedDataDischarged, filtrosParalisacao);

    const duracaoTotal = formattedDataDischarged.reduce((acc, d) => acc + d.duracao, 0);

    const dadosDescarregadoBruto = formattedDataDischarged.reduce((acc, d) => {
        acc.volume += d.volume;
        
        return acc;
    }, { volume: 0});

    await gerarGraficoTotalDescarregado(dadosDescarregado.volume, vesselData[0].volume_manifestado);

    await gerarGraficoDescarregadoPorDia(filteredDataDischarged)

    await gerarGraficoResumoGeral(filteredDataDischarged);

    await gerarGraficoTempoParalisado(filteredDataDischarged);

    await gerarGraficoDescarregadoDiaPeriodo(filteredDataDischarged);

    const pranchaAferidaValor = ((dadosDescarregadoBruto.volume / ((duracaoTotal - somaTempoParalisado) / 60 / 60)) * 24)
    const metaAlcancadaDelta = pranchaAferidaValor - vesselData[0].prancha_minima;

    const metaAlcancadaHTML = metaAlcancadaDelta > 0 ? `<span class="text-target">Meta alcançada: <label class="target-success">+${metaAlcancadaDelta.toFixed(2)}</label></span>` : `<span class="text-target">Meta não alcançada: <label class="target-fail">${metaAlcancadaDelta.toFixed(2)}</label></span>`;

    infoPranchaAferida.innerText = pranchaAferidaValor.toFixed(2);
    infoMetaAlcancada.innerHTML = metaAlcancadaHTML;

    jaFiltradoParalisacao.forEach(item => { 
        if (item == "'undefined'") return;
        paralisacaoSelecionada.innerHTML += `<li class="listagem-paralisacao">- ${item.slice(1, -1)}</li>`;
    })
    const totalVolumeDiaPeriodoLabels = graficoDescarregadoDiaPeriodo.data.labels.length

    if(totalVolumeDiaPeriodoLabels > 10){
            tagGraficoDiaPeriodoContainer.style.minWidth = null;
            tagGraficoDiaPeriodo.style.maxHeight = '100%';
            tagGraficoDiaPeriodo.style.width = 1500 + (totalVolumeDiaPeriodoLabels * 30) +'px';
            graficoDescarregadoDiaPeriodo.options.maintainAspectRatio = false;
        } else {
            tagGraficoDiaPeriodoContainer.style.minWidth = '100%';
            tagGraficoDiaPeriodo.style.width = ''
            graficoDescarregadoDiaPeriodo.options.maintainAspectRatio = true;
    }
    console.log(jaFiltradoParalisacao)
    }