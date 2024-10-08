import { floatParaFloatFormatado, getColorForDate, pbiThemeColors, pbiThemeColorsBorder } from '../../charts_utils.js';

async function gerarGraficoDescarregadoDiaPeriodo(dataDischarged, nomeGrafico) {

    dataDischarged = dataDischarged.map(d => ({
        ...d,
        data_str: (new Date(d.data).getDate()).toString().padStart(2, '0') + '/' + ((new Date(d.data).getMonth() + 1)).toString().padStart(2, '0') // Formata data para 'DD/MM'
    }));

    let naoPossuiDados;
    let possuiDados;

    if (!nomeGrafico.includes('Print')) {
        naoPossuiDados = document.getElementById(`graficoDescarregadoDiaPeriodoEmpty`);
        possuiDados = document.getElementById(nomeGrafico);
        
        possuiDados.style.visibility = 'hidden';
        naoPossuiDados.style.visibility = 'visible';
        naoPossuiDados.style.display = 'block';
    }

    if (dataDischarged.length > 0) {

        if (!nomeGrafico.includes('Print')) {
            naoPossuiDados.style.visibility = 'hidden';
            naoPossuiDados.style.display = 'none';
            possuiDados.style.visibility = 'visible';

        }

        const combinedLabelsDisplay = dataDischarged.map(item => [`${item.data_str}`, `${item.periodo}`]);

        const uniqueCombinedLabelsDisplay = [...new Set(combinedLabelsDisplay)];
        
    // Step 2: Apply the function to your data to generate an array of colors
    // Assuming `dataDischarged` contains objects with a `date` property that is a Date object
    const barColors = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColors, 'date'));
    const barColorsBorder = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColorsBorder, 'date'));

    const dados = {
        labels: uniqueCombinedLabelsDisplay,
        datasets: [
            {
                label: 'Meta',
                data: dataDischarged.map(d => d.meta),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgba(61, 68, 101, 0.8)',
                borderWidth: 2,
                type: 'line',
                lineTension: 0,
                pointBorderWidth: 3,
                pointRadius: 2,
                pointHoverBorderWidth: 4,
                pointHoverRadius: 3,
            },
            {
                label: 'Volume',
                data: dataDischarged.map(d => d.volume),
                backgroundColor: barColors, // Use the generated array of colors
                borderColor: barColorsBorder,
                borderWidth: 1,
            },
        ]
    }

    const options = {
        legend: {
            display: true,
            position: 'chartArea',

        },
        scales: {
            xAxes: [
                {
                gridLines: {
                        display: true
                    },
                    ticks: {
                        fontSize: 12
                    },
                barPercentage: 1,
                    
                },
            ],
            yAxes: [{
                display: false,
                gridLines: {
                    display: false
                },
            }]
        },
        plugins: {
            datalabels: {
                display: true,
                borderRadius: 0,
                padding: 3,
                backgroundColor: (context) => {
                    if (context.dataset.data[context.dataIndex] > 0) {
                        return 'rgba(255, 255, 255, 1)';
                    }
                    return 'rgba(255, 255, 255, 0)'
                },
                borderColor: (context, value) => {
                    return context.dataset.borderColor[context.dataIndex];
                },
                borderWidth: (context) => {
                    if (context.dataset.data[context.dataIndex] > 0) {
                        return 1;
                    }
                    return 0;
                },
                color: 'black',
                anchor: 'start',
                align: 'bottom',
                offset: 5,
                font: {
                    size: 12,                    
                },
                display: (value, context) => {
                    return value.dataset.label === 'Volume'; // Só exibe o valor para o dataset de 'Volume', não para o de Meta
                },
                formatter: (value, context) => {
                    if (value > 0) {
                        return floatParaFloatFormatado(value);  
                    }
                    return '';
                }
            },
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    const valor_formatado = floatParaFloatFormatado(tooltipItem.yLabel);

                    return valor_formatado;
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false,
    }


    if (nomeGrafico.includes('Print')) {
        let optionsPrint = {...options};
        optionsPrint.responsive = false;
        optionsPrint.maintainAspectRatio = true;
        optionsPrint.plugins.datalabels.padding = 1;
        optionsPrint.plugins.datalabels.font.size = 10;
        optionsPrint.scales.xAxes[0].ticks.fontSize = 9;
    
        const graficoDescarregadoDiaPeriodoPrint = new Chart(nomeGrafico, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: dados,
            options: optionsPrint
        });

        return graficoDescarregadoDiaPeriodoPrint;
    }
    // Step 3: Assign the generated colors to `backgroundColor` in your dataset
    const graficoDescarregadoDiaPeriodo = new Chart(nomeGrafico, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: dados,
        options: options
    });


    return graficoDescarregadoDiaPeriodo;
    }
}

export { gerarGraficoDescarregadoDiaPeriodo };