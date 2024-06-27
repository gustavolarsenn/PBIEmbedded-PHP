function cleanFiltersField(fields){
    fields.forEach(field => {
        if (field === 'data') {
            const documentField = document.getElementById(field);
            documentField.value = '';
        } else {
            const documentField = document.getElementById('lista-' + field);
            documentField.innerHTML = '';
        }
    });
    }