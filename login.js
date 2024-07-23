const botaoRegistroLogin = document.getElementById('botao-registro-login');
const formLogin = document.getElementById('form-login');
const formRegistro = document.getElementById('form-registro');
const tituloForm = document.getElementById('titulo-form');
const erroLogin = document.getElementById('erro-login');
const erroRegistro = document.getElementById('erro-registro');

// Altera entre form de login e registro
let toggleLogin = true;

botaoRegistroLogin.addEventListener('click', function() {
    if (toggleLogin) {
        formLogin.style.display = 'none';
        formRegistro.style.display = 'block';
    
        botaoRegistroLogin.innerText = 'Já possui uma conta? Faça login!';

        tituloForm.innerText = 'Registro';
    } else {
        formRegistro.style.display = 'none';
        formLogin.style.display = 'block';
    
        botaoRegistroLogin.innerText = 'Não possui uma conta? Registre-se!';

        tituloForm.innerText = 'Login';
    }
    toggleLogin = !toggleLogin;
});

formLogin.addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(formLogin);
    const actionURL = formLogin.getAttribute('action');
   
    fetch(actionURL, {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Não foi possível fazer requisição!');
        }

        // console.log(response.text())
        return response.json();
    })
    .then(data => {
        if (!data.sucesso) {
            erroLogin.innerText = data.mensagem;
            return 
        }
        window.location.assign('/index.php');
    })
    .catch(error => {
        console.error('Houve algum problema com a requisição:', error, );
    });
});

formRegistro.addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(formRegistro);
    const actionURL = formRegistro.getAttribute('action');
   
    fetch(actionURL, {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Não foi possível fazer requisição!');
        }
        return response.json();
    })
    .then(data => {
        if (!data.sucesso) {
            erroRegistro.innerText = data.mensagem;
            return 
        }
        window.location.assign('/index.php');
    })
    .catch(error => {
        console.error('Houve algum problema com a requisição:', error);
    });
});