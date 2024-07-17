import { colorPalette } from '../../charts_utils.js';

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

        const graficoRealizadoPorao = new Chart('graficoRealizadoPorao', {
            type: 'horizontalBar',
            data: dados,
            options: options 
        });

        return graficoRealizadoPorao;
    }
}

export { gerarGraficoDescarregadoPorao };