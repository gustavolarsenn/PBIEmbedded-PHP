import { convertSecondsToTime } from '../../charts_utils.js';

function aggregateDataByDate(data, date, category) {
    return data.filter(d => d.data === date).map(d => d[category]);
}

async function gerarGraficoTempoParalisado(dadosDescarregado) {

    const dates = [...new Set(dadosDescarregado.map(d => d.data))];

    const naoPossuiDados = document.getElementById('emptyGraficoTempoParalisado');
    const possuiDados = document.getElementById('graficoTempoParalisado');

    const ctx = possuiDados.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 300, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    naoPossuiDados.style.display = 'block';

    if (dadosDescarregado !== null) {

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';
        naoPossuiDados.style.display = 'none';

        // Create datasets for each category
        const datasets = [
            {
                label: 'Chuva',
                data: dates.map(date => aggregateDataByDate(dadosDescarregado, date, 'chuva').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(144, 215, 255, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Transporte',
                data: dates.map(date => aggregateDataByDate(dadosDescarregado, date, 'transporte').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(93, 253, 203, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Força maior',
                data: dates.map(date => aggregateDataByDate(dadosDescarregado, date, 'forca_maior').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(191, 208, 224, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
            {
                label: 'Outros',
                data: dates.map(date => aggregateDataByDate(dadosDescarregado, date, 'outros').reduce((a, b) => a + b, 0)),
                backgroundColor: 'rgba(184, 179, 190, 0.5)',
                borderWidth: 0.5,
                borderColor: 'rgba(61, 68, 101, 1)',
            },
        ];
        
        const data = {
            labels: dates,
            datasets: datasets,
        }

        const options = {
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
            legend: {
                display: true,
                position: 'top',
            },
            responsive: true,
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
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 15,
                    bottom: 15,
                    left: 15,
                    right: 15
                }
            }
        }

        let optionsPrint = {...options};
        optionsPrint.responsive = false;
        optionsPrint.maintainAspectRatio = true;

        // Create the chart
        const graficoTempoParalisado = new Chart('graficoTempoParalisado', {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: data,
            options: options
        });

        // Create the chart
        const graficoTempoParalisadoPrint = new Chart('graficoTempoParalisadoPrint', {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: data,
            options: optionsPrint
        });

        return [graficoTempoParalisado, graficoTempoParalisadoPrint];
    }
}

export { gerarGraficoTempoParalisado };