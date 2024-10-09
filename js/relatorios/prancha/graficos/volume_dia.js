import { floatParaFloatFormatado, getColorForDate } from '../../charts_utils.js';

async function gerarGraficoDescarregadoPorDia(dadosDescarregado, coresAleatorias, coresAleatoriasBordas) {

    dadosDescarregado = dadosDescarregado.map(d => ({
        ...d,
        data_str: (new Date(d.data).getDate()).toString().padStart(2, '0') + '/' + ((new Date(d.data).getMonth() + 1)).toString().padStart(2, '0') // Formata data para 'DD/MM'
    }));

    // 4 - Volume descarregado por dia
    const dadosVolumeDia = dadosDescarregado.reduce((acc, d) => {
        acc[d.data_str] = acc[d.data_str] || { volume: 0 };
        acc[d.data_str].volume += d.volume;
        return acc;
    }, {});

    const dadosVolumeDiaArray = Object.keys(dadosVolumeDia).map(data => ({
        data_str: data,
        volume: dadosVolumeDia[data].volume
    }));

    const dadosMetaDia = dadosDescarregado.reduce((acc, d) => {
        acc[d.data_str] = acc[d.data_str] || { meta: 0 };
        acc[d.data_str].meta += d.meta;
        return acc;
    }, {});

    const dadosMetaDiaArray = Object.keys(dadosMetaDia).map(data => ({
        data_str: data,
        meta: dadosMetaDia[data].meta
    }));

    const naoPossuiDados = document.getElementById('emptyGraficoDescarregadoDia');
    const possuiDados = document.getElementById('graficoDescarregadoDia');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    naoPossuiDados.style.display = 'block';

    if (dadosVolumeDiaArray.length > 0) {

        naoPossuiDados.style.visibility = 'hidden';
        naoPossuiDados.style.display = 'none';
        possuiDados.style.visibility = 'visible';
        
    
    const ctx = possuiDados.getContext('2d');

    const volumePorDiaColors = dadosVolumeDiaArray.map(d => getColorForDate(d.data_str, coresAleatorias, 'string'));
    const volumePorDiaColorsBorder = dadosVolumeDiaArray.map(d => getColorForDate(d.data_str, coresAleatoriasBordas, 'string'));

    const dados = {
        labels: dadosVolumeDiaArray.map(d => d.data_str),
        datasets: [{
                label: 'Meta',
                data: dadosMetaDiaArray.map(d => d.meta),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgba(61, 68, 101, 1)',
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
                data: dadosVolumeDiaArray.map(d => d.volume),
                backgroundColor: volumePorDiaColors,
                borderColor: volumePorDiaColorsBorder,
                pointBorderWidth: 10,
                pointHoverRadius: 10,
                pointHoverBorderWidth: 1,
                pointRadius: 2,
                fill: true,
                borderWidth: 1,
            }
        ]}

    const options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                gridLine: {
                    display: false
                },
                display: false
            }],
            xAxes: [{
                gridLines: {
                    display: true
                },
                barPercentage: 0.75,
            }]
        },
        legend: {
            display: true,
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
        layout: {
            padding: {
                top: 5,
            }
    },
        responsive: true,
        maintainAspectRatio: true,
    }

    const graficoDescarregadoDia = new Chart('graficoDescarregadoDia', {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: dados,
        options: options
    });

    let optionsPrint = {...options};
    optionsPrint.responsive = false;
    optionsPrint.maintainAspectRatio = true;
    optionsPrint.plugins.datalabels.padding = 1;
    optionsPrint.plugins.datalabels.font.size = 10;

    const graficoDescarregadoDiaPrint = new Chart('graficoDescarregadoDiaPrint', {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: dados,
        options: optionsPrint
    });

    return [graficoDescarregadoDia, graficoDescarregadoDiaPrint];
    }
}

export { gerarGraficoDescarregadoPorDia };