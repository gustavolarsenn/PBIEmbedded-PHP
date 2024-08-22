<?php
require_once __DIR__ . '\\..\\config.php'; 

require_once CAMINHO_BASE . '\\SessionManager.php';
require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\PermissoesPagina.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

$pdo = (new Database())->getConnection();
$permissaoPagina = new PermissoesPagina($pdo, basename(__FILE__, ".php"), $_SESSION['tipo_usuario'], null, $_SESSION['id_usuario']);
$possuiPermissao = $permissaoPagina->verificarPermissao();

$urlBase = '/';

if ($possuiPermissao) {
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
				<h2>Bem-vindo, <?php echo $_SESSION['nome'];?>!</h2>
    </div>

    <!-- Required vendors -->
    <script src="<?php echo $urlBase; ?>vendor/global/global.min.js"></script>
    <script src="<?php echo $urlBase; ?>js/quixnav-init.js"></script>
    <script src="<?php echo $urlBase; ?>js/custom.min.js"></script>
	<script src="<?php echo $urlBase; ?>js/logout.js"></script>

    <script src="<?php echo $urlBase; ?>js/pbi/links_pbi.js"></script>
    <script src="<?php echo $urlBase; ?>js/pbi/embed.js"></script>


    <!-- <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script> -->
    
	<!-- Form validate init -->
    <script src="<?php echo $urlBase; ?>js/plugins-init/jquery.validate-init.js"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="<?php echo $urlBase; ?>vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="<?php echo $urlBase; ?>js/plugins-init/chartjs-init.js"></script>


    <!-- Form step init -->
    <script src="<?php echo $urlBase; ?>js/plugins-init/jquery-steps-init.js"></script>

</body>
</html>

<?php
} else {
    include_once CAMINHO_BASE . '\\components\\pagina_desconhecida.php';
}