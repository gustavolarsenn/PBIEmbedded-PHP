import { floatParaStringFormatada } from '../../charts_utils.js';

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

        const graficoVolumeDiaPeriodo = new Chart('graficoVolumeDiaPeriodo', {
            type: 'bar',
            data: dados,
            options: options
        });

        return graficoVolumeDiaPeriodo;
    }
}

export { gerarGraficoVolumeDiaPeriodo };