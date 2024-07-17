import { floatParaFloatFormatado, floatParaStringFormatada } from '../../charts_utils.js';

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
    

    const graficoVolumeDia = new Chart('graficoVolumeDia', {
        type: 'line',
        data: dados,
        options: options
    });

    return graficoVolumeDia;
    }
}

export { gerarGraficoVolumePorDia };