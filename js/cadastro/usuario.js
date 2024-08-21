import { debounce, updateFilters, generateFilters } from '../utils/utils.js';

const tabelaUsuariosBody = document.getElementById('tabela-usuarios-body');
const tabelaUsuariosContainer = document.getElementById('tabela-usuarios-container');
const tabelaUsuariosHeader = document.querySelectorAll('#tabela-usuarios-container thead th');
const botaoRegistro = document.getElementById('botao-registro');
const formRegistroUsuario = document.getElementById('formulario-registro-usuario');
const selectTipo = document.getElementById('tipo');
const nomeUsuarioFilter = document.getElementById('nome-usuario');
const emailUsuarioFilter = document.getElementById('email-usuario');
const botaoConfirmarEdicao = document.getElementById('botao-confirmar-edicao');

const filtros = {
    tiposUsuariosLista: [],
    statusUsuariosLista: [{id: 1, status: 'Ativo'}, {id: 0, status: 'Inativo'}],
    jaFiltradoTipo: [],
    jaFiltradoStatus: [],
    count: 0
}

async function gerarTabelaUsuarios(){
    const filtroNome = nomeUsuarioFilter.value;
    const filtroEmail = emailUsuarioFilter.value;
    const filtroTipo = Array.from(document.getElementById('lista-tipo').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    const filtroStatus = Array.from(document.getElementById('lista-status').querySelectorAll('.multi-select-selected')).map((item) => `'${item.dataset.value}'`)
    
    console.log(filtroStatus)
    filtros.tiposUsuariosLista = await buscarTipoUsuario();
    
    filtros.tiposUsuariosLista.forEach(tipo => {
        selectTipo.innerHTML += `<option value="${tipo.id}">${tipo.tipo}</option>`;
    });

    const tiposUsuariosListaFormatada = filtros.tiposUsuariosLista

    filtros.jaFiltradoTipo = tiposUsuariosListaFormatada
    filtros.jaFiltradoStatus = filtros.statusUsuariosLista
    
    let usuarios = await buscarUsuario();

    console.log(filtroStatus)
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

    console.log(filtros.statusUsuariosLista)
    await carregarUsuarios(usuarios)
    
    if (filtros.count < 1) {
        await generateFilters('tipo', tiposUsuariosListaFormatada, [], async function() { await gerarTabelaUsuarios() }, false);
        await generateFilters('status', filtros.statusUsuariosLista, [], async function() { await gerarTabelaUsuarios() }, false);    
    } else {
        await updateFilters('tipo', tiposUsuariosListaFormatada, filtros.jaFiltradoTipo);
        await updateFilters('status', filtros.statusUsuariosLista, filtros.jaFiltradoStatus);
    }

    filtros.count++;
    
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

async function editarUsuario(email, nome, tipo, status){
    /* Edita usuário no Banco de Dados*/
    const formEditar = document.getElementById('formulario-editar-usuario');

    const formData = new FormData(formEditar);
    const actionURL = formEditar.getAttribute('action');

    formData.get('email-editar') || formData.set('email-editar', email);
    formData.get('nome-editar') || formData.set('nome-editar', nome);
    formData.get('tipo-editar') || formData.set('tipo-editar', tipo);
    formData.get('status-editar') || formData.set('status-editar', status);

    const response = await fetch(actionURL, {
        method: 'POST',
        body: formData,
    })

    const data = await response.json();
}

async function carregarUsuarios(dadosUsuarios) {
    /* Carrega usuários nas linhas da tabela */
    tabelaUsuariosBody.innerHTML = '';
    dadosUsuarios.forEach(usuario => {
        const row = document.createElement('tr');

        const nomeCell = document.createElement('td');
        nomeCell.textContent = usuario.nome;
        row.appendChild(nomeCell);

        const emailCell = document.createElement('td');
        emailCell.textContent = usuario.email;
        row.appendChild(emailCell);

        const tipoCell = document.createElement('td');
        tipoCell.textContent = usuario.tipo;
        row.appendChild(tipoCell);

        const ativoCell = document.createElement('td');
        ativoCell.textContent = usuario.ativo ? 'Sim' : 'Não';
        ativoCell.style.color = usuario.ativo ? 'green' : 'red';
        ativoCell.style.fontWeight = 'bold';
        row.appendChild(ativoCell);

        const actionsCell = document.createElement('td');

        const editButton = document.createElement('button');
        // editButton.textContent = 'Editar';
        editButton.className = 'btn btn-outline-dark icon icon-edit-72';
        editButton.addEventListener('click', () => {
            abrirModalEditar(usuario.email, usuario.nome, usuario.tipo, usuario.ativo);
        });
        actionsCell.appendChild(editButton);

        const deleteButton = document.createElement('button');
        // deleteButton.textContent = 'Excluir';
        deleteButton.className = 'btn btn-outline-danger icon icon-circle-remove';
        deleteButton.addEventListener('click', () => {
            excluirUsuario(usuario.email);
        });
        actionsCell.appendChild(deleteButton);

        row.appendChild(actionsCell);

        tabelaUsuariosBody.appendChild(row);
    });
}

async function abrirModalEditar(email, nome, tipo, status){
    /* Abre modal de edição de usuário */
    $('#modalEditar').modal('show');

    document.getElementById('email-editar').value = email;
    document.getElementById('nome-editar').value = nome;
    const selectTipo = document.getElementById('tipo-editar')//.value = tipo;
    const selectStatus = document.getElementById('status-editar')
    selectTipo.innerHTML = '';
    selectStatus.innerHTML = '';

    filtros.tiposUsuariosLista.forEach(tipoUsuario => {
        let optionTipo = document.createElement('option');
        optionTipo.value = tipoUsuario.id;
        optionTipo.innerText = tipoUsuario.tipo;

        if (tipoUsuario.tipo === tipo) {
            optionTipo.selected = true;
        }
        selectTipo.appendChild(optionTipo);
    });

    filtros.statusUsuariosLista.forEach(statusUsuario => {
        let optionStatus = document.createElement('option');
        optionStatus.value = statusUsuario.id;
        optionStatus.innerText = statusUsuario.status;

        if (statusUsuario.id === status) {
            optionStatus.selected = true;
        }

        selectStatus.appendChild(optionStatus);
    })
        

    botaoConfirmarEdicao.onclick = async function(event){
        const email = document.getElementById('email-editar').value;
        const nome = document.getElementById('nome-editar').value;
        const tipo = document.getElementById('tipo-editar').value;
        const status = document.getElementById('status-editar').value;

        event.preventDefault();
    
        await editarUsuario(email, nome, tipo, status);
        $('#modalEditar').modal('hide');

        const usuarios = await buscarUsuario();
        await carregarUsuarios(usuarios);
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

    const usuarios = await buscarUsuario();
    await carregarUsuarios(usuarios);
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

(async function() {
    await gerarTabelaUsuarios()
})();

$(nomeUsuarioFilter).on('keyup', debounce(async function() {
    await gerarTabelaUsuarios();
}, 300)); // Só faz o filtro após 300ms que o usuário parar de digitar

$(emailUsuarioFilter).on('keyup', debounce(async function() {
    await gerarTabelaUsuarios();
}, 300)); // Só faz o filtro após 300ms que o usuário parar de digitar

$(botaoRegistro).on('click', async function(event){
    event.preventDefault();
    await criarUsuario();
    const usuarios = await buscarUsuario();
    await carregarUsuarios(usuarios);
})