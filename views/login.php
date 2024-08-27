<?php

require_once __DIR__ . '\\..\\config\\config.php'; 

require_once CAMINHO_BASE . '\\models\\SessionManager.php';
require_once CAMINHO_BASE . '\\config\\database.php';

$pdo = (new Database())->getConnection();

$urlBase = '/'
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login</title>

    <link rel="icon" type="image/png" href="<?php echo $urlBase;?>img/icone.png">

    <link href="<?php echo $urlBase;?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase;?>css/login.css" rel="stylesheet">

</head>

<body class="h-100">

    <div class="authincation h-100">
        <div class="container-fluid h-100">
            
		<video autoplay muted loop>
            <source src="<?php echo $urlBase;?>config/background-video.mp4" type="video/mp4">
        </video>
        
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="logo-container">
                                        <img class="logo" src="/img/zport-logo-3x.png">
                                    </div>
                                    <h2 id="titulo-form">Login</h2>
                                    <form id="form-login" method="post" action="<?php echo $urlBase;?>controllers/UsuarioController.php">
                                        <input type="hidden" name="action" value="login">
                                        <span class="ml-2">Email</span>
                                        <input class="form-control" type="text" name="email" placeholder="Email" aria-label="Search" required>
                                        <span class="ml-2">Senha</span>
                                        <input class="form-control" type="password" name="senha" placeholder="Senha" aria-label="Search" required>
                                        <span class="ml-2"></span>
                                        <button type="submit" class="btn btn-primary btn-block" value="Login">Entrar</button>
                                        <div class="mensagem-login-registro" id="erro-login"></div>
                                    </form>

                                    <form id="form-registro" method="post" action="<?php echo $urlBase;?>controllers/UsuarioController.php">
                                        <input type="hidden" name="action" value="registrar">
                                        <span class="ml-2">Nome</span>
                                        <input class="form-control" type="text" name="nome" placeholder="Nome" aria-label="Search" required>
                                        <span class="ml-2">Email</span>
                                        <input class="form-control" type="email" name="email" placeholder="Email" aria-label="Search" required>
                                        <span class="ml-2">Senha</span>
                                        <input class="form-control" type="password" name="senha" placeholder="Senha" aria-label="Search" required>
                                        <span class="ml-2"></span>
                                        <button type="submit" class="btn btn-primary btn-block" value="Registrar">Registrar</button>
                                        <div class="mensagem-login-registro" id="erro-registro"></div>
                                    </form>

                                    <!-- <div class="form-group">
                                        <div class="form-check ml-2">
                                            <input class="form-check-input" type="checkbox" id="basic_checkbox_1">
                                            <label class="form-check-label" for="basic_checkbox_1">Lembre-me</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <a href="page-forgot-password.html">Esqueceu a senha?</a>
                                    </div>
                                    <div class="form-group">
                                        <a href="#" id="botao-registro-login">Não possui conta? Registre-se!</a>
                                    </div> -->
                            </div>
								</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="<?php echo $urlBase;?>login.js"></script>

    <script src="<?php echo $urlBase;?>vendor/global/global.min.js"></script>
    <script src="<?php echo $urlBase;?>js/quixnav-init.js"></script>
    <script src="<?php echo $urlBase;?>js/custom.min.js"></script>

</body>

</html>