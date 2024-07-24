<?php

$basePath = '../../'; // Adjust this path as needed 

include_once $basePath . "SessionManager.php";

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

$actualLink = basename($_GET["reportName"]);

if (isset($_GET['json'])) {
    echo $actualLink;
    exit;
}

// $embedInfo = gerarRelatorioPBI($actualLink);

// if (isset($_GET['json'])) {
//     header('Content-Type: application/json');
//     echo $embedInfo;
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Zport</title>

    <link rel="icon" type="image/png" href="/img/icone.png">

    <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/vendor/owl-carousel/css/owl.theme.default.min.css">
    <link href="<?php echo $basePath; ?>/vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
    <link href="<?php echo $basePath; ?>/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/css/relatorio_pbi.css">
</head>

<body>

    <?php include_once $basePath . '/components/loader.php'?>

    <div id="main-wrapper">

        <?php include_once $basePath . '/components/header.php'?>

        <?php include_once $basePath . '/components/sidebar.php'?>
	
        <div class="content-body">
            <div class="container-fluid">
                <div id="report-action-buttons">
                    <h2 id="report-title"></h2>
                    <div class="report-buttons">
                        <svg xmlns="http://www.w3.org/2000/svg"  id="fullscreen" title="Tela cheia" class="bi bi-arrows-fullscreen" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707m4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707m0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707m-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707"/>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg"  id="download" title="Exportar PDF" class="bi bi-download" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                        </svg>
                    </div>
                </div>

                <!-- <div id="preloader-report">
                    <div class="report-container sk-three-bounce">
                        <div class="sk-child sk-bounce1"></div>
                        <div class="sk-child sk-bounce2"></div>
                        <div class="sk-child sk-bounce3"></div>
                    </div>
                </div> -->
                <section class="report-container" id="report-container">
                    <div class="error-container"></div>
                </section>
    </div>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
    <script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
    <script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>
    
	<script src="<?php echo $basePath; ?>/js/pbi/links_pbi.js"></script>
    <script src="<?php echo $basePath; ?>/js/pbi/embed.js"></script>

    <!-- Required vendors -->
    <script src="<?php echo $basePath; ?>/vendor/global/global.min.js"></script>
    <script src="<?php echo $basePath; ?>/js/quixnav-init.js"></script>
    <script src="<?php echo $basePath; ?>/js/custom.min.js"></script>
	<script src="<?php echo $basePath; ?>/js/logout.js"></script>
    
    <script src="<?php echo $basePath; ?>/vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="<?php echo $basePath; ?>/vendor/jquery-validation/jquery.validate.min.js"></script>
    
	<!-- Form validate init -->
    <script src="<?php echo $basePath; ?>/js/plugins-init/jquery.validate-init.js"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="<?php echo $basePath; ?>/vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="<?php echo $basePath; ?>/js/plugins-init/chartjs-init.js"></script>

    <!-- Form step init -->
    <script src="<?php echo $basePath; ?>/js/plugins-init/jquery-steps-init.js"></script>

    </body>
</html>