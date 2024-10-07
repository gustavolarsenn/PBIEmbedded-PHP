import { floatParaFloatFormatado, getColorForDate } from '../../charts_utils.js';

async function gerarGraficoDescarregadoPorDia(dadosDescarregado, coresAleatorias, coresAleatoriasBordas) {
    // 4 - Volume descarregado por dia
    const dadosVolumeDia = dadosDescarregado.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { volume: 0 };
        acc[d.data].volume += d.volume;
        return acc;
    }, {});

    const dadosVolumeDiaArray = Object.keys(dadosVolumeDia).map(data => ({
        data: data,
        volume: dadosVolumeDia[data].volume
    }));

    const dadosMetaDia = dadosDescarregado.reduce((acc, d) => {
        acc[d.data] = acc[d.data] || { meta: 0 };
        acc[d.data].meta += d.meta;
        return acc;
    }, {});

    const dadosMetaDiaArray = Object.keys(dadosMetaDia).map(data => ({
        data: data,
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

    const volumePorDiaColors = dadosVolumeDiaArray.map(d => getColorForDate(d.data, coresAleatorias));
    const volumePorDiaColorsBorder = dadosVolumeDiaArray.map(d => getColorForDate(d.data, coresAleatoriasBordas));

    const dados = {
        labels: dadosVolumeDiaArray.map(d => d.data),
        datasets: [{
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
            }
        ]}

    const options = {
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
        maintainAspectRatio: false,
    }

    let optionsPrint = {...options};
    optionsPrint.responsive = false;
    optionsPrint.maintainAspectRatio = true;

    const graficoDescarregadoDia = new Chart('graficoDescarregadoDia', {
        type: 'bar',
        data: dados,
        options: options
    });

    const graficoDescarregadoDiaPrint = new Chart('graficoDescarregadoDiaPrint', {
        type: 'bar',
        data: dados,
        options: optionsPrint
    });

    return [graficoDescarregadoDia, graficoDescarregadoDiaPrint];
    }
}

export { gerarGraficoDescarregadoPorDia };