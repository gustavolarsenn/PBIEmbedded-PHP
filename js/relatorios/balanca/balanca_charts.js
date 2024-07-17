import { getVesselData, getUniqueVessels } from './balanca_data.js';
import { renameKeys, assignColorsToList, pbiThemeColors } from '../charts_utils.js';
import { gerarGraficoTotalDescarregado } from './graficos/total_descarregado.js';
import { gerarGraficoDescarregadoPorao } from './graficos/descarregado_porao.js';
import { gerarGraficoClienteArmazemDI } from './graficos/volume_cliente_di_armazem.js';
import { gerarGraficoVolumePorDia } from './graficos/volume_dia.js';
import { gerarGraficoVolumePorCliente } from './graficos/volume_cliente.js';
import { gerarGraficoVolumeDiaPeriodo } from './graficos/volume_dia_periodo.js';

window.cleanFiltersData = cleanFiltersData;

window.addEventListener("load", async function() {
    // Call the generateFilters function here
    generateCharts();
});

var graficoDescarregadoResto, graficoRealizadoPorao, graficoRealizadoClienteDI, graficoVolumeDia, graficoVolumeCliente, graficoVolumeDiaPeriodo;

var count = 0;

var clienteColorMap;

var vesselName = document.getElementById('nome-navio');

var jaFoiFiltradoNavio = '';
var jaFiltradoPeriodo = [];
var jaFiltradoPorao = [];
var jaFiltradoCliente = [];
var jaFiltradoArmazem = [];
var jaFiltradoProduto = [];
var jaFiltradoDI = [];

var count = 0;

const dataField = document.getElementById('data');

// Ao trocar o valor do filtro de data, os gráficos são alterados com os valores atualizados
dataField.addEventListener('change', async function() {
    await generateCharts();
});

async function generateFilters(campo, filterData, condition){
    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };

    let filteredData = filterData.map(item => ({ 0: item, [campo]: item }));
    const renamedFilteredData = filteredData.map(item => renameKeys(item, keyMapping));

    let multiSelectOptions = {
        data: renamedFilteredData,
        placeholder: 'Todos',
        max: null,
        multiple: true,
        search: true,
        selectAll: true,
        count: true,
        keepOpen: true,
        listAll: false,
        onSelect: async function() {
            await generateCharts();
        },
        onUnselect: async function() {
            await generateCharts();
        }
    } 

    if (condition.includes(campo)) {
        multiSelectOptions['max'] = 1;
        multiSelectOptions['selectAll'] = false;
        multiSelectOptions['listAll'] = false;
    } 

    new MultiSelect(`#lista-${campo}`, 
        multiSelectOptions,
    );
}

async function updateFilters(campo, filterData, alreadySelected){
    if (alreadySelected.length < 1) {
    const listaElement = document.getElementById(`lista-${campo}`);
    const allOptions = listaElement.querySelectorAll('[data-value]'); // Select all options
    
        allOptions.forEach(option => {
            const value = option.getAttribute('data-value');
            const isSelected = option.classList.contains('multi-select-selected'); // Check if the option is already selected
        
            if (!filterData.map(String).includes(value) && !isSelected) {
                // If the option is not in filterData and not already selected, hide it
                option.style.display = 'none';
            } else {
                // Otherwise, ensure it's visible
                option.style.display = 'flex';
            }
        });
    }
}

function cleanFiltersData(){
    [jaFiltradoPeriodo, jaFiltradoPorao, jaFiltradoCliente, jaFiltradoArmazem, jaFiltradoProduto, jaFiltradoDI].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';

    generateCharts();
}
async function generateCharts() {
    const listaNavio = await getUniqueVessels();

    // Map through listaNavio, convert each object's values to a Set to remove duplicates, then convert back to array
    const arrayNaviosUnicos = listaNavio.map(obj => [...new Set(Object.values(obj))]);
    
    // Flatten the array of arrays to get a single array with all values
    const listaNaviosUnicos = arrayNaviosUnicos.flat();

    let filtroData = document.getElementById('data').value === '' ? null : [document.getElementById('data').value];

    const filtroNavio = Array.from(document.getElementById('lista-navio').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPeriodo = Array.from(document.getElementById('lista-periodo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroPorao = Array.from(document.getElementById('lista-porao').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroCliente = Array.from(document.getElementById('lista-cliente').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroArmazem = Array.from(document.getElementById('lista-armazem').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroProduto = Array.from(document.getElementById('lista-produto').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroDI = Array.from(document.getElementById('lista-di').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)

    const filtroNavioLimpo = filtroNavio.map(item => item.replace(/^'(.*)'$/, '$1'));

    jaFiltradoPeriodo = filtroPeriodo;
    jaFiltradoPorao = filtroPorao;
    jaFiltradoCliente = filtroCliente;
    jaFiltradoArmazem = filtroArmazem;
    jaFiltradoProduto = filtroProduto;
    jaFiltradoDI = filtroDI;
    
    const navioSelecionado = filtroNavioLimpo.length > 0 ? filtroNavioLimpo[0] : listaNavio[0].navio;

    const dataDischarged = await getVesselData('discharged', navioSelecionado);

    if (navioSelecionado !== jaFoiFiltradoNavio && count > 1) {
        filtroData = null;
        document.getElementById('data').value = ''
        jaFiltradoArmazem = [];
        jaFiltradoCliente = [];
        jaFiltradoDI = [];
        jaFiltradoPeriodo = [];
        jaFiltradoPorao = [];
        jaFiltradoProduto = [];
    }

    vesselName.innerText = navioSelecionado;

    const formattedDataDischarged = dataDischarged.map(item => {
        if (item.data) {
            // Split the date string by space and take the first part (date)
            const formattedDate = item.data.split(' ')[0];

            // Return a new object with the formatted date
            return { ...item, data: formattedDate };
        }
    });

    const dataPlanned = await getVesselData('planned', navioSelecionado);

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataDischarged = formattedDataDischarged.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesData = !filtroData || filtroData.includes(item.data); // Assuming `item.data` is in the same format as `filtroData`
        const matchesPeriodo = jaFiltradoPeriodo.length === 0 || jaFiltradoPeriodo.includes(`'${item.periodo}'`);
        const matchesPorao = jaFiltradoPorao.length === 0 || jaFiltradoPorao.includes(`'${item.porao}'`);
        const matchesCliente = jaFiltradoCliente.length === 0 || jaFiltradoCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = jaFiltradoArmazem.length === 0 || jaFiltradoArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = jaFiltradoProduto.length === 0 || jaFiltradoProduto.includes(`'${item.produto}'`);
        const matchesDI = jaFiltradoDI.length === 0 || jaFiltradoDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesData && matchesPeriodo && matchesPorao && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    // Assuming the structure of each item in `data` is known and matches the filter criteria
    const filteredDataPlanned = dataPlanned.filter((item) => {
        // Check for each filter, if the filter array is not empty and the item's property is included in the filter array
        const matchesNavio = filtroNavio.length === 0 || filtroNavio.includes(`'${item.navio}'`);
        const matchesCliente = jaFiltradoCliente.length === 0 || jaFiltradoCliente.includes(`'${item.cliente}'`);
        const matchesArmazem = jaFiltradoArmazem.length === 0 || jaFiltradoArmazem.includes(`'${item.armazem}'`);
        const matchesProduto = jaFiltradoProduto.length === 0 || jaFiltradoProduto.includes(`'${item.produto}'`);
        const matchesDI = jaFiltradoDI.length === 0 || jaFiltradoDI.includes(`'${item.di}'`);

        // A record must match all active filters to be included
        return matchesNavio && matchesCliente && matchesArmazem && matchesProduto && matchesDI;
    });

    const clientesUnicos = [...new Set(filteredDataDischarged.map(d => d.cliente))];
    if (count < 1) clienteColorMap = assignColorsToList(clientesUnicos, pbiThemeColors);
    
    const listaPeriodo = [...new Set(filteredDataDischarged.map(d => d.periodo))].sort();
    const listaPorao = [...new Set(filteredDataDischarged.map(d => d.porao))].sort();
    const listaCliente = [...new Set(filteredDataDischarged.map(d => d.cliente))].sort();
    const listaArmazem = [...new Set(filteredDataDischarged.map(d => d.armazem))].sort();
    const listaProduto = [...new Set(filteredDataDischarged.map(d => d.produto))].sort();
    const listaDI = [...new Set(filteredDataDischarged.map(d => d.di))].sort();

    if (graficoDescarregadoResto) graficoDescarregadoResto.destroy();
    if (graficoRealizadoPorao) graficoRealizadoPorao.destroy();
    if (graficoRealizadoClienteDI) graficoRealizadoClienteDI.destroy();
    if (graficoVolumeDia) graficoVolumeDia.destroy();
    if (graficoVolumeCliente) graficoVolumeCliente.destroy();
    if (graficoVolumeDiaPeriodo) graficoVolumeDiaPeriodo.destroy();
    
    if (count < 1 || jaFoiFiltradoNavio !== navioSelecionado) {
        if (count < 1) generateFilters('navio', listaNaviosUnicos, ['navio']);
        generateFilters('periodo', listaPeriodo, ['navio']);
        generateFilters('porao', listaPorao, ['navio']);
        generateFilters('cliente', listaCliente, ['navio']);
        generateFilters('armazem', listaArmazem, ['navio']);
        generateFilters('produto', listaProduto, ['navio']);
        generateFilters('di', listaDI, ['navio']);
    } else {
        updateFilters('periodo', listaPeriodo, jaFiltradoPeriodo);
        updateFilters('porao', listaPorao, jaFiltradoPorao);
        updateFilters('cliente', listaCliente, jaFiltradoCliente);
        updateFilters('armazem', listaArmazem, jaFiltradoArmazem);
        updateFilters('produto', listaProduto, jaFiltradoProduto);
        updateFilters('di', listaDI, jaFiltradoDI);
    }
    
    jaFoiFiltradoNavio = navioSelecionado;
    count++;
    
    graficoDescarregadoResto = await gerarGraficoTotalDescarregado(filteredDataDischarged, dataPlanned);

    graficoRealizadoPorao = await gerarGraficoDescarregadoPorao(filteredDataDischarged, filteredDataPlanned);
    
    graficoRealizadoClienteDI = await gerarGraficoClienteArmazemDI(filteredDataDischarged, filteredDataPlanned, clienteColorMap);
    
    graficoVolumeDia = await gerarGraficoVolumePorDia(filteredDataDischarged)

    graficoVolumeCliente = await gerarGraficoVolumePorCliente(filteredDataDischarged, clienteColorMap);
    
    graficoVolumeDiaPeriodo = await gerarGraficoVolumeDiaPeriodo(filteredDataDischarged)
    }