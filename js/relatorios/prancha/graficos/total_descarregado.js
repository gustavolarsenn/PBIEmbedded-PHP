import { floatParaFloatFormatado, colorPalette } from '../../charts_utils.js';

async function gerarGraficoTotalDescarregado(dadosDescarregado, dadosManifestado) {
    // 1 - Total descarregado e restante
    const naoPossuiDados = document.getElementById('emptyGraficoTotalDescarregado');
    const possuiDados = document.getElementById('graficoTotalDescarregado');

    const infoDescarregado = document.getElementById('info-descarregado');
    const infoRestante = document.getElementById('info-restante');

    infoDescarregado.innerText = floatParaFloatFormatado(dadosDescarregado);
    infoRestante.innerText = floatParaFloatFormatado(dadosManifestado - dadosDescarregado);

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    naoPossuiDados.style.display = 'flex';

    if (dadosDescarregado !== null) {

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';
        naoPossuiDados.style.display = 'none';

        const dados = {
            labels: ['Realizado', 'Restante'],
            datasets: [{
                data: [dadosDescarregado, (dadosManifestado - dadosDescarregado) < 0 ? 0 : (dadosManifestado - dadosDescarregado)],
                backgroundColor: [
                    colorPalette['pbiGreenMidHighOpacity'],
                    colorPalette['softBlue']
                ],
                borderColor: [
                    colorPalette['pbiGreenFull'],
                ],
            }]
        }

        const doughnutLabel = {
            id: 'doughnutLabel',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const {ctx, data, chartArea} = chart;
        
                // Calculate the center of the chart
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;
                
                const totalDescarregado = data.datasets[0].data[0];
                const totalManifestado = dadosManifestado;

                const percentDescarregado = floatParaFloatFormatado(((totalDescarregado / totalManifestado) * 100));
        
                // Set the font properties
                ctx.font = 'bold 1.5vw Arial';
                ctx.fillStyle = 'rgba(61, 68, 101, 0.7)';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle'; // Align vertically in the center
        
                // Draw the text in the center of the chart
                ctx.fillText(percentDescarregado + '%', centerX, centerY);
            }
        };

        const options = {
            plugins: {
                doughnutLabel: doughnutLabel
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const valor_formatado = floatParaFloatFormatado(data.datasets[0].data[tooltipItem.index]);
    
                        return valor_formatado;
                    }
                }
            },
            cutoutPercentage: 75,
            responsive: true,
            maintainAspectRatio: true,
        }

        const graficoTotalDescarregado = new Chart('graficoTotalDescarregado', {
            type: 'doughnut',
            data: dados,
            options: options,
            plugins: [doughnutLabel],
        });

        return graficoTotalDescarregado;
    }
}

export { gerarGraficoTotalDescarregado };