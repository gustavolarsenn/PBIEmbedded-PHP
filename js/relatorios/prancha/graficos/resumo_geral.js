import { convertSecondsToTime } from '../../charts_utils.js';

async function gerarGraficoResumoGeral(dadosDescarregado) {
    const categories = ['duracao', 'horas_operacionais' ,'chuva', 'forca_maior', 'transporte'];

    const reducedData = categories.reduce((acc, category) => {
        // Sum the values for the current category
        acc[category] = dadosDescarregado.reduce((sum, d) => sum + d[category], 0);
    
        // Sum the time values for the current category, assuming it's in seconds
        const totalTimeInSeconds = dadosDescarregado.reduce((sum, d) => sum + d[`${category}`], 0);
    
        // Convert total time from seconds to hh:mm:ss format
        const hours = Math.floor(totalTimeInSeconds / 3600);
        const minutes = Math.floor((totalTimeInSeconds % 3600) / 60);
        const seconds = totalTimeInSeconds % 60;
    
        // Format the time string, ensuring two digits for hours, minutes, and seconds
        acc[`${category}_time`] = [hours, minutes, seconds]
            .map(val => val < 10 ? `0${val}` : val)
            .join(':');
    
        return acc;
    }, {});

    const naoPossuiDados = document.getElementById('emptyGraficoResumoGeral');
    const possuiDados = document.getElementById('graficoResumoGeral');

    const ctx = possuiDados.getContext('2d');
    var gradientStroke = ctx.createLinearGradient(500, 0, 300, 0);
    gradientStroke.addColorStop(1, "rgba(128, 182, 244, 0.1)");
    gradientStroke.addColorStop(0, "rgba(61, 68, 101, 0.8)");

    possuiDados.style.visibility = 'hidden';
    naoPossuiDados.style.visibility = 'visible';
    naoPossuiDados.style.display = 'block';

    var maiorValor = 0;

    Object.values(reducedData).map((item) => {
        if (item > maiorValor) {
            maiorValor = item;
        }
    });

    if (dadosDescarregado !== null) {

        naoPossuiDados.style.visibility = 'hidden';
        possuiDados.style.visibility = 'visible';
        naoPossuiDados.style.display = 'none';

        const colors = {
            'Duração': 'rgba(8, 76, 97, 0.5)',
            'Horas operacionais': 'rgba(6, 214, 160, 0.5)',
            'Chuva': 'rgba(144, 215, 255, 0.5)',
            'Força Maior': 'rgba(191, 208, 224, 0.5)',
            'Transporte': 'rgba(93, 253, 203, 0.5)',
        }

        const dados = {
            labels: ['Duração', ['Horas', 'operacionais'], 'Chuva', 'Força Maior', 'Transporte'],
            datasets: [
                {
                    label: 'Tempo',
                    data: Object.keys(reducedData).filter(key => categories.includes(key)).map(key => reducedData[key]),
                    backgroundColor: Object.keys(colors).map(key => colors[key]),
                    borderColor: 'rgba(61, 68, 101, 0.8)',
                    borderWidth: 1,
                }
            ],
        }
        const options = {
            scales: {
                yAxes: [{
                    display: true
                }],
                xAxes: [{
                    display: false
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const value_time = convertSecondsToTime(tooltipItem.xLabel);

                        return value_time;
                    }
                }
            },
            plugins: {
                datalabels: {
                    display: true,
                    borderRadius: 5,
                    padding: 4,
                    color: 'black',
                    anchor: 'end',
                    align: (context) => {
                        if (context.dataset.data[context.dataIndex] / maiorValor > 0.75) {
                            return 'start';
                        }
                        return 'end';
                    },
                    offset: 0,
                    
                    formatter: (value, context) => {
                        if(value > 0){
                            const value_time = convertSecondsToTime(value);
                            return value_time;
                        }
                        return '';
                    }
                },
            },
            layout: {
                padding: {
                    top: 30,
                    bottom: 15,
                    left: 15,
                    right: 15
                },
            },
            responsive: true,
            maintainAspectRatio: true,
        }

        let optionsPrint = {...options};
        optionsPrint.responsive = false;
        optionsPrint.maintainAspectRatio = true;
        
        const graficoResumoGeral = new Chart('graficoResumoGeral', {
            type: 'horizontalBar',
            plugins: [ChartDataLabels],
            data: dados,
            options: options
        });

        const graficoResumoGeralPrint = new Chart('graficoResumoGeralPrint', {
            type: 'horizontalBar',
            plugins: [ChartDataLabels],
            data: dados,
            options: optionsPrint
        });

        return [graficoResumoGeral, graficoResumoGeralPrint];
    }
}

export { gerarGraficoResumoGeral };