<?php
require_once 'SessionManager.php';
SessionManager::checarCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Zport</title>

    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.theme.default.min.css">
    <link href="./vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/pbi_reports.css">
</head>

<body>

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

    <div style="margin: auto; width: 20%;">
        <form method="post" action="Usuario/UsuarioController.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="login">
            <span class="ml-2">Email</span>
            <input class="form-control" type="text" name="email" placeholder="Email" aria-label="Search" required>
            <span class="ml-2">Senha</span>
            <input class="form-control" type="password" name="senha" placeholder="Senha" aria-label="Search" required>
            <input class="btn btn-primary" type="submit" value="Login">
        </form>
        <form method="post" action="Usuario/UsuarioController.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="register">
            <span class="ml-2">Nome</span>
            <input class="form-control" type="text" name="nome" placeholder="Nome" aria-label="Search" required>
            <span class="ml-2">Email</span>
            <input class="form-control" type="text" name="email" placeholder="Email" aria-label="Search" required>
            <span class="ml-2">Senha</span>
            <input class="form-control" type="password" name="senha" placeholder="Senha" aria-label="Search" required>
            <input class="btn btn-primary" type="submit" value="Registrar">
        </form>

        <form method="GET" action="Usuario/UsuarioController.php">
            <input type="hidden" name="action" value="logout">
            <input type="submit" class="btn btn-primary" id="logout" value="Logout"></i>
        </form>
        <div id="error-message"></div>
    </div>
    </div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
    <script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
    <script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
    <script src="./js/quixnav-init.js"></script>
    <script src="./js/custom.min.js"></script>
	<script src="./js/logout.js"></script>
    
    <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
    
	<!-- Form validate init -->
    <script src="./js/plugins-init/jquery.validate-init.js"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="./vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="./js/plugins-init/chartjs-init.js"></script>

    <!-- Form step init -->
    <script src="./js/plugins-init/jquery-steps-init.js"></script>

<script>
$(document).ready(function(){
    $("form").on("submit", function(event){
        event.preventDefault();

        var formValues = $(this).serializeArray();
        var action = formValues.find(item => item.name === 'action').value;

        var request = {
            url: "Usuario/UsuarioController.php",
            method: 'POST',
            data: formValues,
            dataType: 'json'
        };

        if(action === 'logout') {
            request.method = 'GET';
        }

        $.ajax(request).done(function(response) {
            const error = document.getElementById('error-message');
            if(response.error) {
                error.innerHTML = response.error;
            } else {
                switch(action) {
                    case 'login':
                        location.assign('index.php');
                        break;
                    case 'register':
                        location.assign('index.php');
                        break;
                    case 'logout':
                        location.assign('login.php');
                        break;
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
    console.log('AJAX error:', textStatus, errorThrown);
});
});
});
</script>
    </body>
</html>