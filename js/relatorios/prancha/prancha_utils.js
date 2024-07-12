const colorPalette = {
    'sideBarColor': 'rgba(61, 68, 101, 0.6)', // Cor do sidebar (app)
    'pbiGreenMidHighOpacity': 'rgba(55, 167, 148, 0.65)', // Fundo verde (PBI Port Statistics)
    'pbiGreenMidLowOpacity': 'rgba(55, 167, 148, 0.5)', // Fundo verde (PBI Port Statistics)
    'pbiGreenFull': 'rgba(55, 167, 148, 1)', // Borda verde (PBI Port Statistics)
    'softBlue': 'rgba(54, 162, 235, 0.05)', // Donut de resto
    'coolBlue': 'rgba(144, 200, 255, 0.8)'
}

const pbiThemeColors = ["rgba(50, 87, 168, 0.65)","rgba(55, 167, 148, 0.65) ","rgba(139, 61, 136, 0.65)",
    "rgba(221, 107, 127, 0.65)","rgba(107, 145, 201, 0.65)","rgba(245, 200, 105, 0.65)","rgba(119, 196, 168, 0.65)",
    "rgba(222, 166, 207, 0.65)","rgba(186, 74, 197, 0.65)",
    "rgba(197, 74, 83, 0.65)","rgba(254, 226, 102, 0.65)","rgba(62, 155, 128, 0.65)",
    "rgba(197, 74, 145, 0.65)","rgba(37, 69, 181, 0.65)","rgba(128, 22, 137, 0.65)",
    "rgba(137, 22, 30, 0.65)","rgba(22, 31, 137, 0.65)","rgba(137, 22, 88, 0.65)","rgba(24, 45, 121, 0.65)","rgba(15, 21, 92, 0.65)"]

const pbiThemeColorsBorder = ["rgba(50, 87, 168, 1)","rgba(55, 167, 148, 1) ","rgba(139, 61, 136, 1)",
    "rgba(221, 107, 127, 1)","rgba(107, 145, 201, 1)","rgba(245, 200, 105, 1)","rgba(119, 196, 168, 1)",
    "rgba(222, 166, 207, 1)","rgba(186, 74, 197, 1)",
    "rgba(197, 74, 83, 1)","rgba(254, 226, 102, 1)","rgba(62, 155, 128, 1)",
    "rgba(197, 74, 145, 1)","rgba(37, 69, 181, 1)","rgba(128, 22, 137, 1)",
    "rgba(137, 22, 30, 1)","rgba(22, 31, 137, 1)","rgba(137, 22, 88, 1)","rgba(24, 45, 121, 1)","rgba(15, 21, 92, 1)"]


const floatParaFloatFormatado = (valor) => {
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(valor);
}

function getColorForDate(date, colorTheme) {
    // Assuming `date` is a Date object. Adjust the logic if it's a string or another format.

    const dateDate = new Date(date);
    const dayOfWeek = dateDate.getDate(); // getDay() returns 0 for Sunday, 1 for Monday, etc.

    // Let's say weekends are red, weekdays are green
    return colorTheme[dayOfWeek % pbiThemeColors.length];
}

const convertSecondsToTime = (seconds) => {
     // Convert total time from seconds to hh:mm:ss format
     const hours = Math.floor(seconds / 3600);
     const minutes = Math.floor((seconds % 3600) / 60);
 
     // Format the time string, ensuring two digits for hours, minutes, and seconds
     const value_time = [hours, minutes]
         .map(val => val < 10 ? `0${val}` : val)
         .join('h ');
    return value_time + 'm';
}

const paralisacoesSoma = (filteredValues, filteredData, possibleFilters) => {
    const filteredColumnsOnly = filteredData.map(item => {
        const filteredKeys = filteredValues.map(item => Object.keys(possibleFilters).find(key => possibleFilters[key] === item.slice(1, -1)))
        return Object.keys(item).filter(key => filteredKeys.includes(key)).reduce((acc, key) => {
            acc[key] = item[key];
            return acc;
        }, {});
    });
    const somaTempoParalisado = filteredColumnsOnly.reduce((acc, d) => {
        acc.total += Number(d.chuva) || 0;
        acc.total += Number(d.forca_maior) || 0;
        acc.total += Number(d.transporte) || 0;
        acc.total += Number(d.outros) || 0;
    
        return acc;
    }, { total: 0, chuva: 0, forca_maior: 0, transporte: 0, outros: 0 });

    return somaTempoParalisado.total
} 

function renameKeys(obj, keyMap) {
    return Object.keys(obj).reduce((acc, key) => {
        const newKey = keyMap[key] || key; // Use new key name if it exists in the mapping, otherwise use the original key
        acc[newKey] = obj[key]; // Assign the value to the new key in the accumulator object
        return acc;
    }, {}); // Initial value for the accumulator is an empty object
}

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
        multiSelectOptions['multiple'] = false;
        multiSelectOptions['selectAll'] = false;
    } 

    new MultiSelect(`#lista-${campo}`, 
        multiSelectOptions,
    );
}

async function updateFilters(campo, filterData, alreadySelected){
    if (alreadySelected.length < 1) {
    paralisacaoSelecionada.innerText = '';
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
    [jaFiltradoPeriodo, jaFiltradoRelatorio, jaFiltradoParalisacao].forEach(filtro => {
        filtro = [];
    });
    
    count = 0;
    jaFoiFiltradoNavio = '';
    paralisacaoSelecionada.innerHTML = '';

    generateCharts();
}

export { floatParaFloatFormatado, getColorForDate, convertSecondsToTime, paralisacoesSoma, generateFilters, updateFilters, cleanFiltersData, colorPalette, pbiThemeColors, pbiThemeColorsBorder }
