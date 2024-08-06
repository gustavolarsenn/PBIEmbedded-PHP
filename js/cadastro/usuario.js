const tabelaUsuariosBody = document.getElementById('tabela-usuarios-body');
const tabelaUsuariosContainer = document.getElementById('tabela-usuarios-container');
const tabelaUsuariosHeader = document.querySelectorAll('#tabela-usuarios-container thead th');
const botaoRegistro = document.getElementById('botao-registro');
const formRegistroUsuario = document.getElementById('formulario-registro-usuario');
const selectTipo = document.getElementById('tipo');

const botaoConfirmarEdicao = document.getElementById('botao-confirmar-edicao');

/* Filtro */ 
var tiposUsuariosLista, jaFiltradoTipo = [], jaFiltradoStatus = [];
var count = 0;
/* Filtro */ 

async function gerarTabelaUsuarios(){
    /* Filtro */ 
    const filtroNome = document.getElementById('nome-usuario').value;
    const filtroEmail = document.getElementById('email-usuario').value;
    const filtroTipo = Array.from(document.getElementById('lista-tipo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroStatus = Array.from(document.getElementById('lista-status').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    /* Filtro */ 
    
    tiposUsuariosLista = await buscarTipoUsuario();
    
    tiposUsuariosLista.forEach(tipo => {
        selectTipo.innerHTML += `<option value="${tipo.id}">${tipo.tipo}</option>`;
    });
    
    /* Filtro */ 
    const tiposUsuariosListaFormatada = tiposUsuariosLista.map(item => ({
        0: item.id,
        tipo: item.tipo
    }));
    const listaStatusFormatada = [{id: 1, status: 'Ativo'}, {id: 0, status: 'Inativo'}].map(item => ({
        0: item.id,
        status: item.status
    }));

    jaFiltradoTipo = tiposUsuariosListaFormatada
    jaFiltradoStatus = listaStatusFormatada
    /* Filtro */ 
    
    let usuarios = await buscarUsuario();

    /* Filtro */
    if (filtroNome) {
        usuarios = usuarios.filter(usuario => usuario.nome.toLowerCase().includes(filtroNome.toLowerCase()));
    }
    if (filtroEmail) {
        usuarios = usuarios.filter(usuario => usuario.email.toLowerCase().includes(filtroEmail.toLowerCase()));
    }
    if (filtroTipo.length > 0) {
        usuarios = usuarios.filter(usuario => filtroTipo.includes(`'${usuario.id}'`));
    }
    if (filtroStatus.length > 0) {
        usuarios = usuarios.filter(usuario => filtroStatus.includes(`'${usuario.ativo}'`));
    }
    /* Filtro */

    

    await carregarUsuarios(usuarios)
    
    /* Filtro */ 
    if (count < 1) {
        await generateFilters('tipo', tiposUsuariosListaFormatada, [], usuarios);    
        await generateFilters('status', listaStatusFormatada, [], usuarios);    
    } else {
        await updateFilters('tipo', tiposUsuariosListaFormatada, jaFiltradoTipo);
        await updateFilters('status', listaStatusFormatada, jaFiltradoStatus);
    }
    count++;
    /* Filtro */ 
    
    $(tabelaUsuariosContainer).on("scroll", function(){
        if ($(tabelaUsuariosContainer).scrollTop() > 50) {
            $(tabelaUsuariosHeader).css('background-color', 'rgba(61, 68, 101, 1)');
            $(tabelaUsuariosHeader).css('color', 'white');
        } else {
            $(tabelaUsuariosHeader).css('background-color', 'transparent');
            $(tabelaUsuariosHeader).css('color', 'rgba(61, 68, 101, 1)');
        }
    })
}

(async function() {
    await gerarTabelaUsuarios()
})();

$(botaoRegistro).on('click', async function(event){
    event.preventDefault();
    await criarUsuario();
    const usuarios = await buscarUsuario();
    await carregarUsuarios(usuarios);
})

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

async function generateFilters(campo, dados, condicao){
    const keyMapping = {
        0: 'value',
        [campo]: 'text',
    };

    console.log(dados)
    // let filteredData = dados.map(item => ({ 0: item, [campo]: item }));
    const renamedFilteredData = dados.map(item => renameKeys(item, keyMapping));
    
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
            await gerarTabelaUsuarios();
        },
        onUnselect: async function() {
            await gerarTabelaUsuarios();
        }
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

async function buscarTipoUsuario(){
    /* Busca tipos de usuários no banco de dados para listar em select, usando script PHP que faz conexão */
    const response = await fetch('/controllers/TipoUsuarioController.php?action=pegarTiposUsuario', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })

    const data = await response.json();

    return data;
}

async function buscarUsuario(){
    /* Busca usuários no banco de dados para listar em tabela, usando script PHP que faz conexão */

    const response = await fetch('/controllers/UsuarioController.php?action=pegarUsuarios', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })

    const data = await response.json();



    return data;
}

async function carregarUsuarios(dadosUsuarios){
    /* Carrega usuários nas linhas da tabela */
    tabelaUsuariosBody.innerHTML = '';
    dadosUsuarios.forEach(usuario => {
        tabelaUsuariosBody.innerHTML += `
            <tr>
                <td>${usuario.nome}</td>
                <td>${usuario.email}</td>
                <td>${usuario.tipo}</td>
                <td style="color: ${usuario.ativo ? 'green' : 'red'}; font-weight: bold">${usuario.ativo ? 'Sim' : 'Não'}</td>
                <td>
                    <button onclick="abrirModalEditar('${usuario.email}', '${usuario.nome}', '${usuario.tipo}')" class="btn btn-warning">Editar</button>
                    <button onclick="excluirUsuario('${usuario.email}')" class="btn btn-danger">Excluir</button>
                </td>
            </tr>
        `;
    });
}

async function criarUsuario(){
        /* Cria usuário no Banco de Dados */
        try {
            const mensagemCadastroDiv = document.getElementById('erro-cadastro-usuario');
            mensagemCadastroDiv.innerText = '';
    
            const formData = new FormData(formRegistroUsuario);
            const actionURL = formRegistroUsuario.getAttribute('action');
           
            if (formData.get('senha') !== formData.get('confirmarSenha')) {
                mensagemCadastroDiv.innerText = 'As senhas não conferem!';
                return
            }

            const response = await fetch(actionURL, {
                method: 'POST',
                body: formData,
            })
    
            const data = await response.json();
    
            if (!data.sucesso) {
                mensagemCadastroDiv.innerText = data.mensagem;
                return 
            }

            mensagemCadastroDiv.innerText = data.mensagem;
            formRegistroUsuario.reset();

            return data;
        } catch (error) {
            console.error('Houve algum problema com a requisição:', error);
        }
    }

async function excluirUsuario(email){
    /* Exclui usuário do Banco de Dados */
    const response = await fetch('/controllers/UsuarioController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'excluir',
            email: email,
            csrf_token: document.querySelector('input[name="csrf_token"]').value
        }),
    })

    const data = await response.json();
}

async function abrirModalEditar(email, nome, tipo){
    /* Abre modal de edição de usuário */
    $('#modalEditar').modal('show');

    document.getElementById('email-editar').value = email;
    document.getElementById('nome-editar').value = nome;
    const select = document.getElementById('tipo-editar')//.value = tipo;
    select.innerHTML = '';

    tiposUsuariosLista.forEach(tipoUsuario => {
        let option = document.createElement('option');
        option.value = tipoUsuario.id;
        option.innerText = tipoUsuario.tipo;

        if (tipoUsuario.tipo === tipo) {
            option.selected = true;
        }
        select.appendChild(option);
    });

    botaoConfirmarEdicao.onclick = async function(event){
        const email = document.getElementById('email-editar').value;
        const nome = document.getElementById('nome-editar').value;
        const tipo = document.getElementById('tipo-editar').value;
        event.preventDefault();
    
        await editarUsuario(email, nome, tipo);
        $('#modalEditar').modal('hide');
    }
}

async function editarUsuario(email, nome, tipo){
    /* Edita usuário no Banco de Dados*/
    const formEditar = document.getElementById('formulario-editar-usuario');

    const formData = new FormData(formEditar);
    const actionURL = formEditar.getAttribute('action');

    formData.get('email-editar') || formData.set('email-editar', email);
    formData.get('nome-editar') || formData.set('nome-editar', nome);
    formData.get('tipo-editar') || formData.set('tipo-editar', tipo);

    console.log(formData.forEach((value, key) => console.log(key, value)));
    const response = await fetch(actionURL, {
        method: 'POST',
        body: formData,
    })

    const data = await response.json();
}