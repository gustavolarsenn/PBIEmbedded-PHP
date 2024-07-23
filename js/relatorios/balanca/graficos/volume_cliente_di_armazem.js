import { floatParaFloatFormatado } from '../../charts_utils.js';

async function gerarGraficoClienteArmazemDI(dadosDescarregado, dadosPlanejado, mapeamentoCorCliente) {
    // 3 - Realizado por cliente, armazém e DI
    // Tratamento dos dados para o uso no gráfico
    const dadosPlanejadoClienteDI = dadosPlanejado.reduce((acc, d) => {
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`] = acc[`${d.cliente} - ${d.armazem} - ${d.di}`] || { planejado: 0 };
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`].planejado += d.planejado;
        return acc;
    }, {});

    const dadosRealizadoClienteDI = dadosDescarregado.reduce((acc, d) => {
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`] = acc[`${d.cliente} - ${d.armazem} - ${d.di}`] || { peso: 0};
        acc[`${d.cliente} - ${d.armazem} - ${d.di}`].peso += d.peso;
        return acc;
    }, {});
    
    const mergedDados = {};

    // Merge dadosPlanejadoClienteDI into mergedDados
    Object.keys(dadosPlanejadoClienteDI).forEach(key => {
        mergedDados[key] = { ...dadosPlanejadoClienteDI[key] };
    
        if (dadosRealizadoClienteDI[key]) {
            mergedDados[key] = { ...mergedDados[key], ...dadosRealizadoClienteDI[key] };
        }
    });
    
    // Add missing keys from dadosRealizadoClienteDI to mergedDados
    Object.keys(dadosRealizadoClienteDI).forEach(key => {
        if (!mergedDados[key]) {
            mergedDados[key] = { ...dadosRealizadoClienteDI[key] };
        }
    });

    const mergedDadosArray = Object.entries(mergedDados).map(([key, value]) => {
        const [cliente, armazem, di] = key.split(' - ');
        return { cliente, armazem, di, ...value };
    });
    
    const naoPossuiDados = document.getElementById('emptyGraficoRealizadoClienteDI');
    const possuiDados = document.getElementById('graficoRealizadoClienteDI');
    
    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    
    const mergedDadosArrayFiltered = mergedDadosArray.filter(row => "peso" in row);

    const barColorsClienteDI = mergedDadosArrayFiltered.map(d => d.cliente).map(item => ({ item, color: mapeamentoCorCliente[item] }))

    console.log(barColorsClienteDI)

    const dados = {
        labels: mergedDadosArrayFiltered.map(d => d.cliente + " - " + d.armazem + " - " + d.di),
        datasets: [{
            label: 'Realizado',
            data: mergedDadosArrayFiltered.map(d => ((d.peso / d.planejado) * 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: barColorsClienteDI.map(d => d.color),
            borderColor: 'rgba(61, 68, 101, 0.75)',
            borderWidth: 1
        },
        {
            label: 'Restante',
            data: mergedDadosArrayFiltered.map(d => ((1 - (d.peso / d.planejado))* 100).toFixed(2)), // Peso descarregado / planejado
            backgroundColor: 'rgba(54, 162, 235, 0.05)',
            borderColor: 'rgba(54, 162, 235, 0.5)',
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
            layout: {
                padding: {
                    top: 15,
                    bottom: 15,
                    left: 15,
                    right: 15
                }
            },
            plugins: {
                datalabels: {
                    display: (value, context) => {
                        return value.datasetIndex === 0; // Só exibe o valor para o dataset de 'Realizado'
                    },
                    color: 'black',
                    anchor: 'center',
                    align: (value, context) => {
                        return value.dataset.data[value.dataIndex] > 90 ? 'start' : 'end'
                    },
                    offset: 3,
                    formatter: (value, context) => {
                        return floatParaFloatFormatado(value, 0) + '%';
                    }
                },
            },
            responsive: true, 
    }
    if (mergedDadosArrayFiltered.length > 0) {
        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';

    const graficoRealizadoClienteDI = new Chart('graficoRealizadoClienteDI', {
        type: 'horizontalBar',
        plugins: [ChartDataLabels],
        data: dados,
        options: options
    });
    
    return graficoRealizadoClienteDI;
    }
}

export { gerarGraficoClienteArmazemDI };