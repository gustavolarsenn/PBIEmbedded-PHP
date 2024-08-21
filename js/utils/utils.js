/* 
Função para debounce, que evita que a função seja chamada várias vezes seguidas, chamando-a somente depois que o evento trigger ser finalizado. 
Ou seja, se o usuário digitar muito rápido, o filtro só será realizado assim que ele parar de digitar, não afetando tanto a performance.
*/
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/* Filtro */ 
function renameKeys(obj, keyMap) {
    return Object.keys(obj).reduce((acc, key) => {
        const newKey = keyMap[key] || key; // Use new key name if it exists in the mapping, otherwise use the original key
        acc[newKey] = obj[key]; // Assign the value to the new key in the accumulator object
        return acc;
    }, {}); // Initial value for the accumulator is an empty object
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

async function ajustarListaFiltro (lista, campo, listaSimplesBool) {
    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };
    
    if (listaSimplesBool) {
        lista = lista.map(item => ({ 0: item, [campo]: item }));
    }

    const dicionarioRenomeado = lista.map(item => renameKeys(item, keyMapping));

    return dicionarioRenomeado;
}

async function generateFilters(campo, dados, condicao, updateFunction, listaSimplesBool){
    let dadosRenomeados = await ajustarListaFiltro(dados, campo, listaSimplesBool);
    
    let multiSelectOptions = {
        data: dadosRenomeados,
        placeholder: 'Todos',
        max: null,
        multiple: true,
        search: true,
        selectAll: true,
        count: true,
        listAll: false,
        onSelect: updateFunction,
        onUnselect: updateFunction
    } 
    
    if (condicao.includes(campo)) {
        multiSelectOptions['max'] = 1;
        multiSelectOptions['multiple'] = false;
        multiSelectOptions['selectAll'] = false;
    } 
    
    new MultiSelect(`#lista-${campo}`, 
        multiSelectOptions,
    );
}
/* Filtro */ 

export { debounce, renameKeys, updateFilters, generateFilters };