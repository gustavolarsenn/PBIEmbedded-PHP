const tabelaUsuariosBody = document.getElementById('tabela-usuarios-body');
const tabelaUsuariosContainer = document.getElementById('tabela-usuarios-container');
const tabelaUsuariosHeader = document.querySelectorAll('#tabela-usuarios-container thead th');
const botaoRegistro = document.getElementById('botao-registro');
const formRegistroUsuario = document.getElementById('formulario-registro-usuario');

(async function() {
    const usuarios = await buscarUsuario();

    await carregarUsuarios(usuarios)

    $(tabelaUsuariosContainer).on("scroll", function(){
        if ($(tabelaUsuariosContainer).scrollTop() > 50) {
            $(tabelaUsuariosHeader).css('background-color', 'rgba(61, 68, 101, 1)');
        } else {
            $(tabelaUsuariosHeader).css('background-color', 'transparent');
        }
    })

    /* MODAL ???? */
    // $(botaoRegistro).on('click', function(){
    //     $('#modalRegistro').modal('show');
    // })

    $(formRegistroUsuario).on('submit', async function(event){
        await event.preventDefault();
        await criarUsuario();
    })
})();

async function buscarUsuario(){
    /* Busca usuários no banco de dados para listar em tabela, usando script PHP que faz conexão */
    const response = await fetch('/controllers/UsuarioController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'pegarUsuarios'
        }),
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
                <td>
                    <button onclick="editarUsuario('${usuario.email}')" class="btn btn-warning">Editar</button>
                    <button onclick="excluirUsuario('${usuario.email}')" class="btn btn-danger">Excluir</button>
                </td>
            </tr>
        `;
    });
}

async function criarUsuario(){
        const formData = new FormData(formRegistroUsuario);
        const actionURL = formLogin.getAttribute('action');
       
        fetch(actionURL, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Não foi possível fazer requisição!');
            }
    
            console.log(response.text())
            return response.json();
        })
        .then(data => {
            if (!data.sucesso) {
                erroLogin.innerText = data.mensagem;
                return 
            }
            // window.location.assign('/view/index.php');
        })
        .catch(error => {
            console.error('Houve algum problema com a requisição:', error, );
        });
}


async function criarUsuario(){
    /* Cria novo usuário */
    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const tipo = document.getElementById('tipo').value;

    const response = await fetch('/controllers/UsuarioController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'criarUsuario',
            nome,
            email,
            senha,
            tipo
        }),
    })

    console.log(await response.text())
    const data = await response.json();

    if(data.status === 'success'){
        alert('Usuário criado com sucesso!');
        const usuarios = await buscarUsuario();
        await carregarUsuarios(usuarios)
    } else {
        alert('Erro ao criar usuário');
    }
}