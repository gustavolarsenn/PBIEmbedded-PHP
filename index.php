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

    <!-- <link rel="icon" type="image/png" href="../../img/icone/zp-01 - azul.png" sizes="20x20"> -->
    <link rel="icon" type="image/png" href="../../img/icone/zp-01 - azul - redux.png">

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

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        <div class="nav-header">
            <a href="index.php" class="brand-logo">
                <!--<img class="logo-abbr" src="./images/logo-zport-branca-3x.png" alt="">-->
                <img class="logo-compact" src="./images/logo-zport-branca-3x.png" alt="">
                <img class="brand-title" src="./images/logo-zport-branca-3x.png" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="search_bar dropdown">
                                <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <div class="dropdown-menu p-0 m-0">
                                    <form>
                                        <input class="form-control" type="search" placeholder="Pesquisar" aria-label="Search">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-bell"></i>
                                    <div class="pulse-css"></div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="list-unstyled">
                                        <li class="media dropdown-item">
                                            <span class="ml-2"><?php echo $_SESSION['nome']?></span>
                                            
                            <li class="nav-item dropdown header-profile">
                                <!-- <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <i class="mdi mdi-account"></i>
                                </a> -->
                                <div class="dropdown-menu-right">
                                    <a href="./app-profile.html" class="dropdown-item">
                                        <i class="icon-user"></i>
                                        <span class="ml-2">Profile</span>
                                    </a>
                                    <a href="./email-inbox.html" class="dropdown-item">
                                        <i class="icon-envelope-open"></i>
                                        <span class="ml-2">Inbox</span>
                                    </a>
									<a href="#" class="dropdown-item" onclick="logoutConfirmation()" >
										<i class="icon-key"></i>
										<span class="ml-2">Logout</span>
									</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
>
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