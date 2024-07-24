<?php
$basePath = '..'; // Adjust this path as needed 

require 'SessionManager.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Zport</title>

    <link rel="icon" type="image/png" href="/img/icone.png">

    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="./vendor/owl-carousel/css/owl.theme.default.min.css">
    <link href="./vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
</head>

<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js"></script>
<script src="https://microsoft.github.io/PowerBI-JavaScript/demo/node_modules/powerbi-client/dist/powerbi.js"></script>
<script src="http://code.jquery.com/jquery-2.0.3.min.js" type="text/javascript" ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <?php include_once 'components/loader.php'?>

    <div id="main-wrapper">
        <?php include_once 'components/header.php'?>

        <?php include_once 'components/sidebar.php'?>
	
        <div class="content-body">
            <div class="container-fluid">
				<h2>Acompanhamento da Operação <?php echo $_SESSION['nome'];?></h2>
    </div>

    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
    <script src="./js/quixnav-init.js"></script>
    <script src="./js/custom.min.js"></script>
	<script src="./js/logout.js"></script>
	<script src="links_pbi.js"></script>
    <script src="embed.js"></script>

    <script src="<?php echo $basePath; ?>/js/pbi/links_pbi.js"></script>
    <script src="<?php echo $basePath; ?>/js/pbi/embed.js"></script>


    <!-- <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script> -->
    
	<!-- Form validate init -->
    <script src="./js/plugins-init/jquery.validate-init.js"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="./vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="./js/plugins-init/chartjs-init.js"></script>


    <!-- Form step init -->
    <script src="./js/plugins-init/jquery-steps-init.js"></script>

</body>

</html>