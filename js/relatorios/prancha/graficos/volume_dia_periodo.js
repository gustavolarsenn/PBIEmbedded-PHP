import { floatParaFloatFormatado, getColorForDate, pbiThemeColors, pbiThemeColorsBorder } from '../../charts_utils.js';

async function gerarGraficoDescarregadoDiaPeriodo(dataDischarged) {
    const naoPossuiDados = document.getElementById('emptyGraficoDescarregadoDiaPeriodo');
    const possuiDados = document.getElementById('graficoDescarregadoDiaPeriodo');

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    naoPossuiDados.style.display = 'block';

    if (dataDischarged.length > 0) {

        naoPossuiDados.style.visibility = 'hidden';
        naoPossuiDados.style.display = 'none';
        possuiDados.style.visibility = 'visible';

        const combinedLabelsDisplay = dataDischarged.map(item => [`${item.data}`, `${item.periodo}`]);
        const uniqueCombinedLabelsDisplay = [...new Set(combinedLabelsDisplay)];
        
    // Step 2: Apply the function to your data to generate an array of colors
    // Assuming `dataDischarged` contains objects with a `date` property that is a Date object
    const barColors = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColors));
    const barColorsBorder = dataDischarged.map(d => getColorForDate(d.data, pbiThemeColorsBorder));

    const dados = {
        labels: uniqueCombinedLabelsDisplay,
        datasets: [
            {
                label: 'Meta',
                data: dataDischarged.map(d => d.meta),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgba(61, 68, 101, 0.8)',
                borderWidth: 4,
                type: 'line',
                lineTension: 0,
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
            display: false
        },
        scales: {
            xAxes: [{
                position: 'bottom',
                gridLines: {
                    display: false
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    display: true
                }
            }]
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

    // Step 3: Assign the generated colors to `backgroundColor` in your dataset
    const graficoDescarregadoDiaPeriodo = new Chart('graficoDescarregadoDiaPeriodo', {
        type: 'bar',
        data: dados,
        options: options
    });

    return graficoDescarregadoDiaPeriodo;
    }
}

export { gerarGraficoDescarregadoDiaPeriodo };