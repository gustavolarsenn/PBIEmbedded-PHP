import { getVesselInfo, getVesselData, getUniqueVessels } from './prancha_data.js';
import { floatParaFloatFormatado, paralisacoesSoma, pbiThemeColors, pbiThemeColorsBorder } from '../charts_utils.js';
import { gerarGraficoTotalDescarregado } from './graficos/total_descarregado.js';
import { gerarGraficoDescarregadoPorDia } from './graficos/volume_dia.js';
import { gerarGraficoResumoGeral } from './graficos/resumo_geral.js';
import { gerarGraficoTempoParalisado } from './graficos/tempo_paralisado.js';
import { gerarGraficoDescarregadoDiaPeriodo } from './graficos/volume_dia_periodo.js';
import { generateFilters, updateFilters } from '../../utils/utils.js';

window.cleanFiltersData = cleanFiltersData;

document.addEventListener('DOMContentLoaded', function () {
    generateCharts();
});

const botaoHamburger = document.querySelector('.hamburger');

var tagGraficoDiaPeriodo = document.getElementById('graficoDescarregadoDiaPeriodo');
var tagGraficoDiaPeriodoContainer = document.getElementById('descarregado-dia-periodo-container');
var tagGraficoDiaPeriodoContainerGrafico = document.getElementById('descarregado-dia-periodo-grafico');

var infoVesselTag = document.getElementById('info-navio-titulo');
var infoBerthTag = document.getElementById('info-berth');
var infoProductTag = document.getElementById('info-product');
var infoModalityTag = document.getElementById('info-modality');
var infoVolumeTag = document.getElementById('info-volume');
var infoDateTag = document.getElementById('info-date');
var infoMinimumDischargeTag = document.getElementById('info-minimum-discharge');

var infoPranchaAferida = document.getElementById('prancha-aferida');
var infoMetaAlcancada = document.getElementById('meta-alcancada');

var paralisacaoSelecionada = document.getElementById('paralisacao-selecionada');

var jaFoiFiltradoNavio = '';
var jaFiltradoRelatorio = [];
var jaFiltradoPeriodo = [];
var jaFiltradoParalisacao = [];

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

var graficoTotalDescarregado, graficoDescarregadoDia, graficoDescarregadoDiaSideBar, graficoResumoGeral, graficoTempoParalisado, graficoDescarregadoDiaPeriodo;

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

    // Map through listaNavio, convert each object's values to a Set to remove duplicates, then convert back to array
    const arrayNaviosUnicos = listaNavio.map(obj => [...new Set(Object.values(obj))]);
    
    // Flatten the array of arrays to get a single array with all values
    const listaNaviosUnicos = arrayNaviosUnicos.flat();

    let filtroData = document.getElementById('data').value === '' ? null : [document.getElementById('data').value];

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroRelatorio = Array.from(document.getElementById('lista-relatorio_no').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroMotivoParalisacao = Array.from(document.getElementById('lista-motivo_paralisacao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    
    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoRelatorio = filtroRelatorio;
    jaFiltradoParalisacao = filtroMotivoParalisacao;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].navio;

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
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = jaFiltradoPeriodo.length === 0 || jaFiltradoPeriodo.includes(`'${item.periodo}'`);
        const matchesRelatorio = jaFiltradoRelatorio.length === 0 || jaFiltradoRelatorio.includes(`'${item.relatorio_no}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesData && matchesPeriodo && matchesRelatorio;
    });

    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))].sort();
    const listaRelatorio = [...new Set(filteredDataDischarged.map(d => d.relatorio_no))].sort();
    
    if (graficoTotalDescarregado) graficoTotalDescarregado.destroy();
    if (graficoDescarregadoDia) graficoDescarregadoDia.destroy();
    if (graficoDescarregadoDiaSideBar) graficoDescarregadoDiaSideBar.destroy();
    if (graficoResumoGeral) graficoResumoGeral.destroy();
    if (graficoTempoParalisado) graficoTempoParalisado.destroy();
    if (graficoDescarregadoDiaPeriodo) graficoDescarregadoDiaPeriodo.destroy();
    
    if (count < 1 || jaFoiFiltradoNavio !== navioSelecionado) {
        if (count < 1) generateFilters('navio', listaNaviosUnicos, ['navio'], async function() {await generateCharts();}, true);
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

    graficoTotalDescarregado = await gerarGraficoTotalDescarregado(dadosDescarregado.volume, vesselData[0].volume_manifestado);

    [graficoDescarregadoDia, graficoDescarregadoDiaSideBar] = await gerarGraficoDescarregadoPorDia(filteredDataDischarged, shuffledColors, shuffledColorsBorder)

    graficoResumoGeral = await gerarGraficoResumoGeral(filteredDataDischarged);

    graficoTempoParalisado = await gerarGraficoTempoParalisado(filteredDataDischarged);

    graficoDescarregadoDiaPeriodo = await gerarGraficoDescarregadoDiaPeriodo(filteredDataDischarged);

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

    if(totalVolumeDiaPeriodoLabels > 10){
            tagGraficoDiaPeriodoContainerGrafico.style.minWidth = null;
            tagGraficoDiaPeriodo.style.maxHeight = '100%';
            tagGraficoDiaPeriodo.style.width = 1500 + (totalVolumeDiaPeriodoLabels * 30) +'px';
            graficoDescarregadoDiaPeriodo.options.maintainAspectRatio = true;
            tagGraficoDiaPeriodoContainer.style.overflowX = 'scroll';
        } else {
            tagGraficoDiaPeriodoContainerGrafico.style.minWidth = '100%';
            tagGraficoDiaPeriodo.style.width = ''
            graficoDescarregadoDiaPeriodo.options.maintainAspectRatio = true;
            tagGraficoDiaPeriodoContainer.style.overflowX = 'hidden';
        }
    }