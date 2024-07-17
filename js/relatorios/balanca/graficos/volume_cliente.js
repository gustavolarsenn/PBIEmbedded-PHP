import { floatParaFloatFormatado, floatParaStringFormatada } from '../../charts_utils.js';

async function gerarGraficoVolumePorCliente(dadosDescarregado, mapeamentoCorCliente) {
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

    const barColorCliente = clientesUnicos.map(item => ({ item, color: mapeamentoCorCliente[item] }))
    
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

        const graficoVolumeCliente = new Chart('graficoVolumeCliente', {
            type: 'horizontalBar',
            plugins: [ChartDataLabels],
            data: dados,
            options: options
        });
        return graficoVolumeCliente;
    }
}

export { gerarGraficoVolumePorCliente };