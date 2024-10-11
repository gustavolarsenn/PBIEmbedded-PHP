import { getVesselInfo, getVesselData, getUniqueVessels } from './prancha_data.js';
import { floatParaFloatFormatado, paralisacoesSoma, pbiThemeColors, pbiThemeColorsBorder, ajustarInformacoesNavioImpressao} from '../charts_utils.js';
import { gerarGraficoTotalDescarregado } from './graficos/total_descarregado.js';
import { gerarGraficoDescarregadoPorDia } from './graficos/volume_dia.js';
import { gerarGraficoResumoGeral } from './graficos/resumo_geral.js';
import { gerarGraficoTempoParalisado } from './graficos/tempo_paralisado.js';
import { gerarGraficoDescarregadoDiaPeriodo } from './graficos/volume_dia_periodo.js';
import { generateFilters, updateFilters } from '../../utils/utils.js';
import { gerarPDF } from '../gerarPDF.js';

window.cleanFiltersData = cleanFiltersData;

function graficoScroll(totalLinhasTabela, graficoScroll, grafico, containerGrafico, container){
    
    const maxLinhas = window.innerWidth < 1440 ? 8 : 16; 
    const widthMultiplier = grafico.id == 'graficoDescarregadoDiaPeriodo' ? 65 : 20;
    if(totalLinhasTabela > maxLinhas){
        grafico.style.display = 'none';
        grafico.style.visibility = 'hidden';
        graficoScroll.style.display = 'block';
        graficoScroll.style.visibility = 'visible';
        
        containerGrafico.style.minWidth = null;
        graficoScroll.style.width = 1500 + (totalLinhasTabela * widthMultiplier) +'px';
        container.classList.add('grafico-scroll');
    } else {
        grafico.style.display = 'block';
        grafico.style.visibility = 'visible';
        graficoScroll.style.display = 'none';
        graficoScroll.style.visibility = 'hidden';
        
        graficoScroll.style.width = '';
        containerGrafico.style.minWidth = '100%';
        
        // graficoScroll.style.width = 'auto';
        container.classList.remove('grafico-scroll');
        }
    }

document.addEventListener('DOMContentLoaded', function () {
    generateCharts();
});

var containerTempoParalisado = document.getElementById('tempo-paralisado-container');

var tagGraficoDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');
var tagGraficoDiaPeriodoScroll = document.getElementById('graficoDescarregadoDiaPeriodoScroll');
var tagGraficoDiaPeriodoContainer = document.getElementById('descarregado-dia-periodo-container');
var tagGraficoDiaPeriodoContainerGrafico = document.getElementById('descarregado-dia-periodo-grafico');

var infoPortRow = document.getElementById('info-port-row');
var infoVesselRow = document.getElementById('info-vessel-row');
var infoBerthRow = document.getElementById('info-berth-row');
var infoProductRow = document.getElementById('info-product-row');
var infoModalityRow = document.getElementById('info-modality-row');
var infoVolumeRow = document.getElementById('info-volume-row');
var infoDateRow = document.getElementById('info-date-row');
var infoMinimumDischargeRow = document.getElementById('info-minimum-discharge-row');


var navioContainer = document.getElementById('info-navio-container');

var infoVesselTag = document.getElementById('info-navio-titulo');
var infoPortTag = document.getElementById('info-port');
var infoBerthTag = document.getElementById('info-berth');
var infoProductTag = document.getElementById('info-product');
var infoModalityTag = document.getElementById('info-modality');
var infoVolumeTag = document.getElementById('info-volume');
var infoDateTag = document.getElementById('info-date');
var infoMinimumDischargeTag = document.getElementById('info-minimum-discharge');

var infoPranchaAferida = document.getElementById('prancha-aferida');
var infoMetaAlcancada = document.getElementById('meta-alcancada');

var paralisacaoSelecionada = document.getElementById('paralisacao-selecionada');
const botaoHamburger = document.querySelector('.hamburger');

const botaoExportarPDF = document.getElementById('export-pdf');

var jaFoiFiltradoNavio = '';
var jaFiltradoRelatorio = [];
var jaFiltradoPeriodo = [];
var jaFiltradoParalisacao = [];
var listaGraficos = []

var count = 0;
var clicked = false;

const dataField = document.getElementById('data');

botaoHamburger.addEventListener('click', function() {
    const descarregadoDia = document.getElementById('graficoDescarregadoDia');
    const descarregadoDiaSideBar = document.getElementById('graficoDescarregadoDiaSideBar');

    if (clicked) {
        descarregadoDiaSideBar.style.visibility = 'hidden !important'
        descarregadoDiaSideBar.style.display = 'none !important'
        descarregadoDiaSideBar.style.maxHeight = '0 !important'

        descarregadoDia.style.visibility = 'visible !important'
        descarregadoDia.style.display = 'block !important'
        descarregadoDia.style.maxHeight = '100% !important'

        clicked = false;
    } else {
        descarregadoDia.style.visibility = 'hidden !important'
        descarregadoDia.style.display = 'none !important'
        descarregadoDia.style.maxHeight = '0 !important'

        descarregadoDiaSideBar.style.visibility = 'visible !important'
        descarregadoDiaSideBar.style.display = 'block !important'
        descarregadoDiaSideBar.style.maxHeight = '100% !important'

        clicked = true;
    }
})

// Step 1: Define a function to determine the color based on the date
const shuffledColors = pbiThemeColors.sort(() => 0.5 - Math.random()); // Shuffle the colors array
const shuffledColorsBorder = pbiThemeColorsBorder.sort(() => 0.5 - Math.random()); // Shuffle the colors array

var graficoTotalDescarregado, graficoTotalDescarregadoPrint,
graficoDescarregadoDia, graficoDescarregadoDiaPrint, graficoDescarregadoDiaScroll, 
graficoResumoGeral, graficoResumoGeralPrint, 
graficoTempoParalisado, graficoTempoParalisadoPrint, graficoTempoParalisadoScroll,
graficoDescarregadoDiaPeriodo, graficoDescarregadoDiaPeriodoPrint, graficoDescarregadoDiaPeriodoScroll;

var count = 0;

let filtrosParalisacao = {
    'chuva': 'Chuva',
    'forca_maior': 'Força Maior',
    'transporte': 'Transporte',
    'outros': 'Outros'
};

function cleanFiltersData(){
    [jaFiltradoPeriodo, jaFiltradoRelatorio, jaFiltradoParalisacao].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';
    paralisacaoSelecionada.innerHTML = '';

    generateCharts();
}


// Ao trocar o valor do filtro de data, os gráficos são alterados com os valores atualizados
dataField.addEventListener('change', async function() {
    await generateCharts();
});

async function generateCharts() {
    const listaNavio = await getUniqueVessels();

    const arrayViagensUnicas = [...new Set(listaNavio)];

    // Map through listaNavio, convert each object's values to a Set to remove duplicates, then convert back to array
    const arrayNaviosUnicos = [...new Set(listaNavio.map(obj => obj.navio))];

    // Flatten the array of arrays to get a single array with all values
    const listaNaviosUnicos = arrayNaviosUnicos.flat();
    
    const listaNaviosUnicosFormatados = arrayViagensUnicas.map(navio => ({1: navio.navio, value: navio.id_viagem, id: navio.id_viagem, navio: navio.navio}));

    let filtroData = document.getElementById('data').value === '' ? null : [document.getElementById('data').value];

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroRelatorio = Array.from(document.getElementById('lista-relatorio_no').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroMotivoParalisacao = Array.from(document.getElementById('lista-motivo_paralisacao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    
    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoRelatorio = filtroRelatorio;
    jaFiltradoParalisacao = filtroMotivoParalisacao;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].id_viagem;

    const dataDischarged = await getVesselData(navioSelecionado);

    const vesselData = await getVesselInfo(navioSelecionado);

    if (navioSelecionado !== jaFoiFiltradoNavio && count > 1) {
        filtroData = null;
        document.getElementById('data').value = ''
        jaFiltradoPeriodo = [];
        jaFiltradoRelatorio = [];
        jaFiltradoParalisacao = [];
        paralisacaoSelecionada.innerHTML = '';
    }

    ajustarInformacoesNavioImpressao('beforeprint', infoProductRow, vesselData[0].produto.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoProductRow, vesselData[0].produto.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoVesselRow, vesselData[0].navio.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoVesselRow, vesselData[0].navio.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoPortRow, vesselData[0].porto.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoPortRow, vesselData[0].porto.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoBerthRow, vesselData[0].berco.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoBerthRow, vesselData[0].berco.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoModalityRow, vesselData[0].modalidade.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoModalityRow, vesselData[0].modalidade.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoVolumeRow, vesselData[0].volume_manifestado.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoVolumeRow, vesselData[0].volume_manifestado.length, 17);

    ajustarInformacoesNavioImpressao('beforeprint', infoMinimumDischargeRow, vesselData[0].prancha_minima.length, 17);
    ajustarInformacoesNavioImpressao('afterprint', infoMinimumDischargeRow, vesselData[0].prancha_minima.length, 17);

    infoPortTag.innerText = vesselData[0].porto;
    infoVesselTag.innerText = vesselData[0].navio;
    infoBerthTag.innerText = vesselData[0].berco;
    infoProductTag.innerText = vesselData[0].produto;
    infoModalityTag.innerText = vesselData[0].modalidade;
    infoVolumeTag.innerText = floatParaFloatFormatado(vesselData[0].volume_manifestado);
    infoDateTag.innerText = vesselData[0].data.split(' ')[0];
    infoMinimumDischargeTag.innerText = floatParaFloatFormatado(vesselData[0].prancha_minima);

    const formattedDataDischarged = dataDischarged.map(item => {
        if (item.data) {
            // Split the date string by space and take the first part (date)
            const formattedDate = item.data.split(' ')[0];

            // Return a new object with the formatted date
            return { ...item, data: formattedDate };
        }
    });

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataDischarged = formattedDataDischarged.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        // const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = jaFiltradoPeriodo.length === 0 || jaFiltradoPeriodo.includes(`'${item.periodo}'`);
        const matchesRelatorio = jaFiltradoRelatorio.length === 0 || jaFiltradoRelatorio.includes(`'${item.relatorio_no}'`);

        // A record must match all active filters to be included
        // return matchesNavio && matchesData && matchesPeriodo && matchesRelatorio;
        return matchesData && matchesPeriodo && matchesRelatorio;
    });

    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))].sort();
    const listaRelatorio = [...new Set(filteredDataDischarged.map(d => d.relatorio_no))].sort();
    
    if (graficoTotalDescarregado) graficoTotalDescarregado.destroy();
    if (graficoTotalDescarregadoPrint) graficoTotalDescarregadoPrint.destroy();

    if (graficoDescarregadoDia) graficoDescarregadoDia.destroy();
    if (graficoDescarregadoDiaPrint) graficoDescarregadoDiaPrint.destroy();
    if (graficoDescarregadoDiaScroll) graficoDescarregadoDiaScroll.destroy();

    if (graficoResumoGeral) graficoResumoGeral.destroy();
    if (graficoResumoGeralPrint) graficoResumoGeralPrint.destroy();

    if (graficoTempoParalisado) graficoTempoParalisado.destroy();
    if (graficoTempoParalisadoPrint) graficoTempoParalisadoPrint.destroy();
    if (graficoTempoParalisadoScroll) graficoTempoParalisadoScroll.destroy();

    if (graficoDescarregadoDiaPeriodo) graficoDescarregadoDiaPeriodo.destroy();
    if (graficoDescarregadoDiaPeriodoScroll) graficoDescarregadoDiaPeriodoScroll.destroy();


    if(listaGraficos){
        for (let i = 0; i < listaGraficos.length; i++) {
            const grafico = listaGraficos[i];
            if (grafico) {
                document.getElementById(grafico.titleBlock.ctx.canvas.id).remove();
                grafico.destroy();
            }
        }
        listaGraficos = []
    }

    if (count < 1 || jaFoiFiltradoNavio !== navioSelecionado) {
        if (count < 1) generateFilters('navio', listaNaviosUnicosFormatados, ['navio'], async function() {await generateCharts();}, false);
        generateFilters('periodo', listaPeriodo, ['navio'], async function() {await generateCharts();}, true);
        generateFilters('relatorio_no', listaRelatorio, ['navio'], async function() {await generateCharts();}, true);
        generateFilters('motivo_paralisacao', Object.values(filtrosParalisacao), ['navio'], async function() {await generateCharts();}, true);
    } else {
        updateFilters('periodo', listaPeriodo, jaFiltradoPeriodo);
        updateFilters('relatorio_no', listaRelatorio, jaFiltradoRelatorio);
    }
    
    jaFoiFiltradoNavio = navioSelecionado;
    count++;

    const dadosDescarregado = filteredDataDischarged.reduce((acc, d) => {
        acc.volume += d.volume;
        
        return acc;
    }, { volume: 0});

    const somaTempoParalisado = paralisacoesSoma(jaFiltradoParalisacao, filteredDataDischarged, filtrosParalisacao);

    const duracaoTotal = filteredDataDischarged.reduce((acc, d) => acc + d.duracao, 0);

    [graficoTotalDescarregado, graficoTotalDescarregadoPrint] = await gerarGraficoTotalDescarregado(dadosDescarregado.volume, vesselData[0].volume_manifestado);

    graficoDescarregadoDia = await gerarGraficoDescarregadoPorDia(filteredDataDischarged, shuffledColors, shuffledColorsBorder, 'graficoDescarregadoDia');
    graficoDescarregadoDiaPrint = await gerarGraficoDescarregadoPorDia(filteredDataDischarged, shuffledColors, shuffledColorsBorder, 'graficoDescarregadoDiaPrint');
    graficoDescarregadoDiaScroll = await gerarGraficoDescarregadoPorDia(filteredDataDischarged, shuffledColors, shuffledColorsBorder, 'graficoDescarregadoDiaScroll');

    [graficoResumoGeral, graficoResumoGeralPrint] = await gerarGraficoResumoGeral(filteredDataDischarged);

    graficoTempoParalisado = await gerarGraficoTempoParalisado(filteredDataDischarged, 'graficoTempoParalisado');
    graficoTempoParalisadoPrint = await gerarGraficoTempoParalisado(filteredDataDischarged, 'graficoTempoParalisadoPrint');
    graficoTempoParalisadoScroll = await gerarGraficoTempoParalisado(filteredDataDischarged, 'graficoTempoParalisadoScroll');

    let loops, newLoops;
    const uniqueDates = filteredDataDischarged.map(d => d.data).filter((value, index, self) => self.indexOf(value) === index);
    if (filteredDataDischarged.length > 16){

        loops = uniqueDates.length / 4;
        let ajustarUltimos = uniqueDates.length % 4 < 2 ? true : false;
        
        newLoops = [];

        for (let i = 0; i < Math.ceil(loops); i++) {
    
            if (ajustarUltimos && i == Math.ceil(loops) - 2) {
                newLoops.push(uniqueDates.slice(i * 4, ((i + 1) * 4) - 1));
            } else if (ajustarUltimos && i == Math.ceil(loops) - 1) {
                newLoops.push(uniqueDates.slice(i * 4 - 1, ((i + 1) * 4)));
            } else {
                newLoops.push(uniqueDates.slice(i * 4, (i + 1) * 4));
            }
        }
    } else {
        loops = 1;
        newLoops = [uniqueDates];
    }

        graficoDescarregadoDiaPeriodo = await gerarGraficoDescarregadoDiaPeriodo(filteredDataDischarged, 'graficoDescarregadoDiaPeriodo');
        graficoDescarregadoDiaPeriodoScroll = await gerarGraficoDescarregadoDiaPeriodo(filteredDataDischarged, 'graficoDescarregadoDiaPeriodoScroll');

        newLoops.forEach(async (loop, i) => {
            let graficoPrintAtual;
            const nomeGrafico = 'graficoDescarregadoDiaPeriodoPrint' + i;

            const canvasElement = document.createElement('canvas');
            canvasElement.id = nomeGrafico;
            canvasElement.classList.add('graficoParaPDF');
            canvasElement.height = '185';
            canvasElement.width = '985';

            if (i === Math.floor(loops)){
                canvasElement.style.marginBottom = '15px';
            } else {
                canvasElement.style.marginBottom = 'auto';
            }

            if (i === 0) {
                graficoPrintAtual = document.getElementById('graficoDescarregadoDiaPeriodo');
            } else {
                graficoPrintAtual = document.getElementById('graficoDescarregadoDiaPeriodoPrint' + (i - 1));
            } 

            graficoPrintAtual.insertAdjacentElement('afterend', canvasElement);

            const slicedData = filteredDataDischarged.filter(d => loop.includes(d.data));
            listaGraficos.push(await gerarGraficoDescarregadoDiaPeriodo(slicedData, nomeGrafico));
        });
    
    if (Math.ceil(loops) === 2){
        containerTempoParalisado.classList.add('page-break');
    } else {
        containerTempoParalisado.classList.remove('page-break');
    }
    const pranchaAferidaValor = ((dadosDescarregado.volume / ((duracaoTotal - somaTempoParalisado) / 60 / 60)) * 24)
    const metaAlcancadaDelta = pranchaAferidaValor - vesselData[0].prancha_minima;

    const metaAlcancadaHTML = metaAlcancadaDelta > 0 ? `<span class="text-target">Meta alcançada: <label class="target-success">+${floatParaFloatFormatado(metaAlcancadaDelta)}</label></span>` : `<span class="text-target">Meta não alcançada: <label class="target-fail">${floatParaFloatFormatado(metaAlcancadaDelta)}</label></span>`;

    infoPranchaAferida.innerText = floatParaFloatFormatado(pranchaAferidaValor)
    infoMetaAlcancada.innerHTML = metaAlcancadaHTML;

    paralisacaoSelecionada.innerHTML = '';
    jaFiltradoParalisacao.forEach(item => {
        if (item == "'undefined'") return;
        paralisacaoSelecionada.innerHTML += `<li class="listagem-paralisacao">- ${item.slice(1, -1)}</li>`;
    })

    
    const totalVolumeDiaPeriodoLabels = graficoDescarregadoDiaPeriodo.data.labels.length
    
    graficoScroll(totalVolumeDiaPeriodoLabels, tagGraficoDiaPeriodoScroll, tagGraficoDiaPeriodo, tagGraficoDiaPeriodoContainerGrafico, tagGraficoDiaPeriodoContainer)
    graficoScroll(graficoDescarregadoDia.data.labels.length, document.getElementById('graficoDescarregadoDiaScroll'), document.getElementById('graficoDescarregadoDia'), document.getElementById('descarregado-dia-grafico'), document.getElementById('descarregado-dia-container'))
    graficoScroll(graficoTempoParalisado.data.labels.length, document.getElementById('graficoTempoParalisadoScroll'), document.getElementById('graficoTempoParalisado'), document.getElementById('tempo-paralisado-grafico'), document.getElementById('tempo-paralisado-container'))
}

