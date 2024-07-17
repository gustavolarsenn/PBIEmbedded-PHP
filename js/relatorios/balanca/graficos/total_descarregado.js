import { floatParaFloatFormatado, colorPalette } from '../../charts_utils.js';

async function gerarGraficoTotalDescarregado(dadosDescarregado, dadosPlanejado) {
    // 1 - Total descarregado e restante
    const dadosDescarregadoResto = dadosDescarregado.reduce((acc, d) => {
        acc.peso += d.peso;
        return acc;
    }, { peso: 0 });

    const dadosPlanejadoAgrupado = dadosPlanejado.reduce((acc, d) => {
        acc.planejado += d.planejado;
        return acc;
    }
    , { planejado: 0 });

    const naoPossuiDados = document.getElementById('emptyGraficoDescarregadoResto');
    const possuiDados = document.getElementById('graficoDescarregadoResto');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';

    if (dadosDescarregadoResto.peso !== null) {
        
        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

        const dados = {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregadoResto.peso, dadosPlanejadoAgrupado.planejado - dadosDescarregadoResto.peso],
                backgroundColor: [
                    colorPalette['pbiGreenMidHighOpacity'],
                    'rgba(54, 162, 235, 0.05)'
                ],
                borderColor: [
                    colorPalette['softBlue'],
                ],
            }]
        }

        const options = {
            legend: {
                display: false
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10,
                    left: 10,
                    right: 10
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
            cutoutPercentage: 80,
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
        
                const percentDescarregado = floatParaFloatFormatado(((totalDescarregado / totalManifestado) * 100));
        
                // Set the font properties
                ctx.font = 'bold 1.5vw Arial';
                ctx.fillStyle = 'rgba(61, 68, 101, 0.7)';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle'; // Align vertically in the center
        
                // Draw the text in the center of the chart
                ctx.fillText(percentDescarregado + '%', centerX, centerY);
            }
        }

        const graficoDescarregadoResto = new Chart('graficoDescarregadoResto', {
            type: 'doughnut',
            data: dados,
            plugins: [doughnutLabel],
            options: options
        });

        return graficoDescarregadoResto;
    }
}

export { gerarGraficoTotalDescarregado };