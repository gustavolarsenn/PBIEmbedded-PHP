<?php
require_once __DIR__ . '\\..\\..\\config.php'; 

require_once CAMINHO_BASE . '\\SessionManager.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

// TODO: Somente ADMIN pode acessar essa página

$urlBase = '/'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Zport</title>

    <link rel="icon" type="image/png" href="<?php echo $urlBase;?>img/icone.png">

    <link rel="stylesheet" href="<?php echo $urlBase; ?>vendor/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo $urlBase; ?>vendor/owl-carousel/css/owl.theme.default.min.css">
    <link href="<?php echo $urlBase; ?>vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/cadastro/usuario.css" rel="stylesheet">
</head>

<body>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
<script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
<script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <?php include_once CAMINHO_BASE . '\\components\\loader.php'?>

    <div id="main-wrapper">
        <?php include_once CAMINHO_BASE . '\\components\\header.php'?>

        <?php include_once CAMINHO_BASE . '\\components\\sidebar.php'?>
	
        <div class="content-body">
            <div class="container-fluid">
				<h2>Cadastro usuários</h2>

                <div class="row">
                    <div class="col-lg-12 main-container">
                        <div class="card container" id="formulario-registro">
                            <div class="card-body">
                                    <form id="formulario-registro-usuario" method="post" action="<?php echo $urlBase;?>controllers/UsuarioController.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="register">
                                    <div class="form-group">
                                        <label for="nome">Nome</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="senha">Senha</label>
                                        <input type="password" class="form-control" id="senha" name="senha" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="confirmarSenha">Confirmar senha</label>
                                        <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="tipo">Tipo de usuário</label>
                                        <select class="form-control" id="tipo" required>
                                            <option value="ADMIN">ADMIN</option>
                                            <option value="USER">USER</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3" id="botao-registro">Cadastrar</button>
                                </form>
                            </div>
                        </div>  
                        <div class="card container" id="tabela-usuarios-container">
                            <div class="card-body" id="card-body-table">
                                <table class="table table-striped" id="tabela-usuarios-final">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Tipo de usuário</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabela-usuarios-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>  
                    </div>
                </div>
                                        
    </div>

    <!-- Required vendors -->
    <script src="<?php echo $urlBase; ?>vendor/global/global.min.js"></script>
    <script src="<?php echo $urlBase; ?>js/quixnav-init.js"></script>
    <script src="<?php echo $urlBase; ?>js/custom.min.js"></script>
	<script src="<?php echo $urlBase; ?>js/logout.js"></script>

    <script src="<?php echo $urlBase; ?>js/pbi/links_pbi.js"></script>

    <script src="<?php echo $urlBase; ?>js/cadastro/usuario.js"></script>

    <!-- <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script> -->
    
	<!-- Form validate init -->
    <script src="<?php echo $urlBase; ?>js/plugins-init/jquery.validate-init.js"></script>

    <!-- Form step init -->
    <script src="<?php echo $urlBase; ?>js/plugins-init/jquery-steps-init.js"></script>

</body>

</html>