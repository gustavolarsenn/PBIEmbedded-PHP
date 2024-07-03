import { getVesselInfo, getVesselData, getUniqueVessels } from './prancha_data.js';

window.cleanFiltersData = cleanFiltersData;

window.addEventListener("load", async function() {
    // Call the generateFilters function here
    generateCharts();
});

var graficoTotalDescarregado, graficoDescarregadoDia, graficoResumoGeral, graficoTempoParalisado, graficoDescarregadoDiaPeriodo;

var graficoResumoGeral;

var count = 0;

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

var vesselName = document.getElementById('vessel-name');

var infoVesselTag = document.getElementById('info-vessel');
var infoBerthTag = document.getElementById('info-berth');
var infoProductTag = document.getElementById('info-product');
var infoModalityTag = document.getElementById('info-modality');
var infoVolumeTag = document.getElementById('info-volume');
var infoDateTag = document.getElementById('info-date');
var infoMinimumDischargeTag = document.getElementById('info-minimum-discharge');

var jaFoiFiltradoNavio = '';
var jaFiltradoRelatorio = [];
var jaFiltradoPeriodo = [];

var count = 0;

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
        multiSelectOptions['multiple'] = false;
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
    [jaFiltradoPeriodo, jaFiltradoRelatorio].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';

    generateCharts();
}

async function gerarGraficoTotalDescarregado(dataDischarged, valor_manifestado) {
    // 1 - Total descarregado e restante
    const dadosDescarregadoResto = dataDischarged.reduce((acc, d) => {
        acc.volume += d.volume;
        return acc;
    }, { volume: 0 });

    const noDataGraficoDescarregadoResto = document.getElementById('emptyGraficoTotalDescarregado');
    const dataGraficoDescarregadoResto = document.getElementById('graficoTotalDescarregado');

    dataGraficoDescarregadoResto.style.visibility = 'hidden';
    noDataGraficoDescarregadoResto.style.visibility = 'visible';
    noDataGraficoDescarregadoResto.style.display = 'flex';
    if (dadosDescarregadoResto.volume !== null) {
        noDataGraficoDescarregadoResto.style.visibility = 'hidden';
        dataGraficoDescarregadoResto.style.visibility = 'visible';
        noDataGraficoDescarregadoResto.style.display = 'none';

        graficoTotalDescarregado = new Chart('graficoTotalDescarregado', {
        type: 'doughnut',
        data: {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregadoResto.volume, valor_manifestado - dadosDescarregadoResto.volume],
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
            responsive: true,
            maintainAspectRatio: true,
        }
    });

    }
}

async function gerarGraficoDescarregadoPorDia(dataDischarged) {
    // 4 - Volume descarregado por dia
    console.log(dataDischarged)
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
    var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

    var gradientFill = ctx.createLinearGradient(500, 0, 300, 0);
    gradientFill.addColorStop(1, "rgba(128, 182, 244, 0.3)");
    gradientFill.addColorStop(0, "rgba(61, 68, 101, 0.3)");

    graficoDescarregadoDia = new Chart('graficoDescarregadoDia', {
        type: 'bar',
        data: {
            labels: dadosVolumeDiaArray.map(d => d.data),
            datasets: [
                {
                label: 'Volume',
                data: dadosVolumeDiaArray.map(d => d.volume),
                backgroundColor: gradientFill,
                borderColor: "rgba(61, 68, 101, 1)",
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
            },
            {
                label: 'Meta',
                data: dadosMetaDiaArray.map(d => d.meta),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgba(61, 68, 101, 0.8)',
                borderWidth: 4,
                type: 'line',
                lineTension: 0,
            }
        ]
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

        graficoResumoGeral = new Chart('graficoResumoGeral', {
            type: 'bar',
            data: {
                labels: ['Tempo'],
                datasets: [
                    {
                        label: 'Chuva',
                        data: [reducedData.chuva],
                        backgroundColor: 'rgba(144, 215, 255, 0.5)',
                        borderWidth: 0.5,
                        borderColor: 'rgba(61, 68, 101, 1)',
                    },
                    {
                    label: 'Transporte',
                    data: [reducedData.transporte],
                    backgroundColor: 'rgba(93, 253, 203, 0.5)',
                    borderWidth: 0.5,
                    borderColor: 'rgba(61, 68, 101, 1)',
                    },
                    {
                    label: 'Força maior',
                    data: [reducedData.forca_maior],
                    backgroundColor: 'rgba(191, 208, 224, 0.5)',
                    borderWidth: 0.5,
                    borderColor: 'rgba(61, 68, 101, 1)',
                    },
                    {
                    label: 'Duração',
                    data: [reducedData.duracao],
                    backgroundColor: 'rgba(8, 76, 97, 0.5)',
                    borderColor: "rgba(61, 68, 101, 1)",
                    borderWidth: 1,
                    },
                    {
                    label: 'Operacionais',
                    data: [reducedData.horas_operacionais],
                    backgroundColor: 'rgba(6, 214, 160, 0.5)',
                    borderColor: "rgba(61, 68, 101, 1)",
                    borderWidth: 1,
                    },
            ]
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
                    display: true,
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            // return `${data.datasets[tooltipItem.datasetIndex].label}: ${tooltipItem.yLabel}`;
                            return `${data.datasets[tooltipItem.datasetIndex].label}: ${reducedData[`${categories[tooltipItem.index]}_time`]}`;
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15,
                        left: 15,
                        right: 15
                    },
                }
            },
        });
    }
}

async function gerarGraficoTempoParalisado(dataDischarged) {

    const categories = ['chuva', 'forca_maior', 'transporte', 'duracao', 'horas_operacionais'];

    const dates = [...new Set(dataDischarged.map(d => d.data))];

    console.log(dates)

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
                            display: false
                        },
                        barPercentage: 0.75,
                    }],
                    yAxes: [{
                        stacked: true,
                        display: false,
                    }],
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
    // 5 - Volume descarregado por dia e por período
    // const dadosVolumeDiaPeriodo = dataDischarged.reduce((acc, d) => {
    //     // Check if the date key exists, if not initialize it
    //     if (!acc[d.data]) {
    //         acc[d.data] = {};
    //     }
    //     // Check if the periodo key exists within the date, if not initialize it
    //     if (!acc[d.data][d.periodo]) {
    //         acc[d.data][d.periodo] = { volume: 0 };
    //     }
    //     // Accumulate volume
    //     acc[d.data][d.periodo].volume += d.volume;
    //     return acc;
    // }, {});


    // const dadosVolumeDiaPeriodoArray = Object.keys(dadosVolumeDiaPeriodo).map(data => ({
    //     data: data,
    //     volume: dadosVolumeDiaPeriodo[data].volume
    // }));

    const noDataGraficoVolumeDiaPeriodo = document.getElementById('emptyGraficoDescarregadoDiaPeriodo');
    const dataGraficoVolumeDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');

    dataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
    noDataGraficoVolumeDiaPeriodo.style.visibility = 'visible';
    noDataGraficoVolumeDiaPeriodo.style.display = 'block';
    // if (dadosVolumeDiaPeriodoArray.length > 0) {
    if (dataDischarged.length > 0) {
        noDataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
        noDataGraficoVolumeDiaPeriodo.style.display = 'none';
        dataGraficoVolumeDiaPeriodo.style.visibility = 'visible';

        const ctx = dataGraficoVolumeDiaPeriodo.getContext('2d');
        var gradientStroke = ctx.createLinearGradient(500, 0, 300, 0);
        gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
        gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

        console.log(dataDischarged)

        const uniqueDatas = [...new Set(dataDischarged.map(item => item.data))];
        const uniquePeriods = [...new Set(dataDischarged.map(item => item.periodo))];

        graficoDescarregadoDiaPeriodo = new Chart('graficoDescarregadoDiaPeriodo', {
            type: 'bar',
            data: {
                datasets: uniqueDatas.map(date => {
                    const data = dataDischarged.filter(d => d.data === date).map(d => d.volume);
                    return {
                        label: date,
                        data: data,
                        backgroundColor: gradientStroke,
                        borderColor: 'rgba(82, 183, 136, 0.8)',
                        borderWidth: 1,
                        xAxisID: 'xAxisPeriod', // Assign to the custom x-axis
                    };
                })
            },
            options: {
                scales: {
                    xAxes: [
                        {
                            id: 'xAxisDate', // Custom x-axis ID
                            // labels: uniqueDatas,
                            type: 'category',
                            position: 'bottom',
                        },
                        {
                            id: 'xAxisPeriod', // Custom x-axis ID
                            type: 'category',
                            position: 'bottom',
                            labels: uniquePeriods,
                        }
                    ],
                }
            }
        })
    }
}

// async function gerarGraficoDescarregadoDiaPeriodo(dataDischarged) {
//     // 5 - Volume descarregado por dia e por período
//     const dadosVolumeDiaPeriodo = dataDischarged.reduce((acc, d) => {
//         // Check if the date key exists, if not initialize it
//         if (!acc[d.data]) {
//             acc[d.data] = {};
//         }
//         // Check if the periodo key exists within the date, if not initialize it
//         if (!acc[d.data][d.periodo]) {
//             acc[d.data][d.periodo] = { volume: 0 };
//         }
//         // Accumulate volume
//         acc[d.data][d.periodo].volume += d.volume;
//         return acc;
//     }, {});


//     const dadosVolumeDiaPeriodoArray = Object.keys(dadosVolumeDiaPeriodo).map(data => ({
//         data: data,
//         volume: dadosVolumeDiaPeriodo[data].volume
//     }));

//     const noDataGraficoVolumeDiaPeriodo = document.getElementById('emptyGraficoDescarregadoDiaPeriodo');
//     const dataGraficoVolumeDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');

//     dataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
//     noDataGraficoVolumeDiaPeriodo.style.visibility = 'visible';
//     noDataGraficoVolumeDiaPeriodo.style.display = 'block';
//     if (dadosVolumeDiaPeriodoArray.length > 0) {
//         noDataGraficoVolumeDiaPeriodo.style.visibility = 'hidden';
//         noDataGraficoVolumeDiaPeriodo.style.display = 'none';
//         dataGraficoVolumeDiaPeriodo.style.visibility = 'visible';

//         const labels = Object.keys(dadosVolumeDiaPeriodo); // Dates for the x-axis

// // Assuming you want a dataset for each period across all dates
// const periods = new Set(); // To track unique periods
// labels.forEach(date => {
//     Object.keys(dadosVolumeDiaPeriodo[date]).forEach(period => {
//         periods.add(period);
//     });
// });


// console.log(periods)

// let periodsArray = [];

// const datasets = Array.from(periods).map(period => {
//     console.log(period)
//     periodsArray.push(period)
//     const data = labels.map(date => {
//         return dadosVolumeDiaPeriodo[date][period] ? dadosVolumeDiaPeriodo[date][period].volume : 0;
//     });
//     return {
//         label: period,
//         data: data,
//         backgroundColor: 'rgba(82, 183, 136, 0.5)',
//         borderColor: 'rgba(82, 183, 136, 0.8)',
//         borderWidth: 1,
//         xAxisId: 'xAxis2' // Uncomment and configure if using a second x-axis
//     };
// });

// console.log(labels)

// const chartConfig = {
//     type: 'bar',
//     data: {
//         labels: labels, // Dates as labels
//         datasets: datasets.map(dataset => ({
//             ...dataset,
//             // Conditionally assign datasets to an x-axis based on a new property or condition
//             xAxisID: dataset.useSecondAxis ? 'xAxis2' : 'xAxis1',
//         }))
//     },
//     options: {
//         ...barOptions,
//         tooltips: {},
//         responsive: true,
//         scales: {
//             xAxes: [{
//                 id: 'xAxis1',
//                 type: 'category',
//                 position: 'bottom',
//             }, {
//                 // Second x-axis configuration
//                 id: 'xAxis2',
//                 type: 'category',
//                 position: 'bottom', // Position it at the top or wherever you prefer
//                 // Additional configuration for the second x-axis
//                 gridLines: {
//                     display: false // Hide grid lines for the second x-axis or customize as needed
//                 },
//                 ticks: {
//                     // Customization for the second x-axis ticks
//                 }
//             }],
//             yAxes: []
//         }
//     }
// };

// // Note: Ensure datasets include a `useSecondAxis` property or similar to conditionally assign them to 'xAxis2'.

// graficoDescarregadoDiaPeriodo = new Chart('graficoDescarregadoDiaPeriodo', chartConfig);
//     }
// }


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

    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoRelatorio = filtroRelatorio;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].navio;


    const dataDischarged = await getVesselData(navioSelecionado);

    const vesselData = await getVesselInfo(navioSelecionado);

    if (navioSelecionado !== jaFoiFiltradoNavio && count > 1) {
        filtroData = null;
        document.getElementById('data').value = ''
        jaFiltradoPeriodo = [];
        jaFiltradoRelatorio = [];
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
    } else {
        updateFilters('periodo', listaPeriodo, jaFiltradoPeriodo);
        updateFilters('relatorio_no', listaRelatorio, jaFiltradoRelatorio);
    }
    
    jaFoiFiltradoNavio = navioSelecionado;
    count++;

    await gerarGraficoTotalDescarregado(filteredDataDischarged, vesselData[0].volume_manifestado);

    await gerarGraficoDescarregadoPorDia(filteredDataDischarged)

    await gerarGraficoResumoGeral(filteredDataDischarged);

    await gerarGraficoTempoParalisado(filteredDataDischarged);

    await gerarGraficoDescarregadoDiaPeriodo(filteredDataDischarged);
    // await gerarGraficoVolumePorCliente(filteredDataDischarged);
    // await gerarGraficoDescarregadoPorao(filteredDataDischarged, filteredDataPlanned);
    
    // await gerarGraficoClienteArmazemDI(filteredDataDischarged, filteredDataPlanned);
    

    
    // await gerarGraficoVolumeDiaPeriodo(filteredDataDischarged)
    }