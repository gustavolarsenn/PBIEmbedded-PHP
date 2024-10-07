
const colorPalette = {
    'sideBarColor': 'rgba(61, 68, 101, 0.6)', // Cor do sidebar (app)
    'pbiGreenMidHighOpacity': 'rgba(55, 167, 148, 0.65)', // Fundo verde (PBI Port Statistics)
    'pbiGreenMidLowOpacity': 'rgba(55, 167, 148, 0.5)', // Fundo verde (PBI Port Statistics)
    'pbiGreenFull': 'rgba(55, 167, 148, 1)', // Borda verde (PBI Port Statistics)
    'softBlue': 'rgba(54, 162, 235, 0.05)', // Donut de resto
    'coolBlue': 'rgba(144, 200, 255, 0.8)'
}

const opacityThemeColors = '0.6'

const pbiThemeColors = [`rgba(50, 87, 168, ${opacityThemeColors})`,`rgba(55, 167, 148, ${opacityThemeColors})`,`rgba(139, 61, 136, ${opacityThemeColors})`,
    `rgba(221, 107, 127, ${opacityThemeColors})`,`rgba(107, 145, 201, ${opacityThemeColors})`,`rgba(245, 200, 105, ${opacityThemeColors})`,`rgba(119, 196, 168, ${opacityThemeColors})`,
    `rgba(222, 166, 207, ${opacityThemeColors})`,`rgba(186, 74, 197, ${opacityThemeColors})`,
    `rgba(197, 74, 83, ${opacityThemeColors})`,`rgba(254, 226, 102, ${opacityThemeColors})`,`rgba(62, 155, 128, ${opacityThemeColors})`,
    `rgba(197, 74, 145, ${opacityThemeColors})`,`rgba(37, 69, 181, ${opacityThemeColors})`,`rgba(128, 22, 137, ${opacityThemeColors})`,
    `rgba(137, 22, 30, ${opacityThemeColors})`,`rgba(22, 31, 137, ${opacityThemeColors})`,`rgba(137, 22, 88, ${opacityThemeColors})`,`rgba(24, 45, 121, ${opacityThemeColors})`,`rgba(15, 21, 92, ${opacityThemeColors})`]

const pbiThemeColorsBorder = ["rgba(50, 87, 168, 1)","rgba(55, 167, 148, 1)","rgba(139, 61, 136, 1)",
    "rgba(221, 107, 127, 1)","rgba(107, 145, 201, 1)","rgba(245, 200, 105, 1)","rgba(119, 196, 168, 1)",
    "rgba(222, 166, 207, 1)","rgba(186, 74, 197, 1)",
    "rgba(197, 74, 83, 1)","rgba(254, 226, 102, 1)","rgba(62, 155, 128, 1)",
    "rgba(197, 74, 145, 1)","rgba(37, 69, 181, 1)","rgba(128, 22, 137, 1)",
    "rgba(137, 22, 30, 1)","rgba(22, 31, 137, 1)","rgba(137, 22, 88, 1)","rgba(24, 45, 121, 1)","rgba(15, 21, 92, 1)"]


function floatParaFloatFormatado(valor, casasDecimais = 2){
    return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: casasDecimais, maximumFractionDigits: casasDecimais }).format(valor);
}

function floatParaStringFormatada(valor){
    if(String(valor).length === 6){
            return String(valor / 1_000).substring(0, 4) + 'K';
    } else if (String(valor).length === 7 || String(valor).length === 8 || String(valor).length === 9){
            return String(valor / 1_000_000).substring(0, 4) + 'M';
    } else if (String(valor).length === 10 || String(valor).length === 11 || String(valor).length === 12){
            return String(valor / 1_000_000_000).substring(0, 4) + 'B';
    } else {
        return valor;
    }
    }

function convertSecondsToTime(seconds){
        // Convert total time from seconds to hh:mm:ss format
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
    
        // Format the time string, ensuring two digits for hours, minutes, and seconds
        const value_time = [hours, minutes]
            .map(val => val < 10 ? `0${val}` : val)
            .join('h ');
    return value_time + 'm';
}

function paralisacoesSoma(filteredValues, filteredData, possibleFilters){
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

function getColorForDate(date, colorTheme, type) {
    // Assuming `date` is a Date object. Adjust the logic if it's a string or another format.
    let dayOfWeek;

    if (type === 'date'){
        const dateDate = new Date(date);
        dayOfWeek = dateDate.getDate();
    } else {
        dayOfWeek = date.substring(0, 2);
    }

    // Let's say weekends are red, weekdays are green
    return colorTheme[dayOfWeek % pbiThemeColors.length];
}

function assignColorsToList(list, colorTheme) {
    // Step 1: Identify unique values
    const uniqueValues = [...new Set(list)];

    // Step 2: Assign colors to unique values
    const colorMapping = uniqueValues.reduce((acc, value, index) => {
        acc[value] = colorTheme[index % colorTheme.length];
        return acc;
    }, {});

    // Step 3: Map original list to colors, preserving order
    return colorMapping;
}

function ajustarInformacoesNavioImpressao(acao, elemento, quantidadeChar, limiteChar){

    if (acao === 'afterprint'){
        window.addEventListener(acao, () => {
            if (quantidadeChar > limiteChar) {
                elemento.style.flexDirection = 'row';
                elemento.style.alignItems = 'center';
            }
        })
    }

    if (acao === 'beforeprint'){
        window.addEventListener(acao, () => {
            if (quantidadeChar > limiteChar) {
                elemento.style.flexDirection = 'column';
                elemento.style.alignItems = 'flex-start';
            }
        })
    }
}

export { floatParaFloatFormatado, convertSecondsToTime, paralisacoesSoma, renameKeys, getColorForDate, assignColorsToList, floatParaStringFormatada, colorPalette, pbiThemeColors, pbiThemeColorsBorder, ajustarInformacoesNavioImpressao }