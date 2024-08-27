<?php 

$urlBase = '/'

?>

<div id="header-wrap">
    <div class="nav-header">
        <a href="/views/index.php" class="brand-logo">
            <img class="logo-abbr" id='logo-mobile' src="/images/logo-zport-branca-3x.png" alt="">
            <!-- <img class="logo-compact" src="/images/logo-zport-branca-3x.png" alt=""> -->
            <img class="brand-title" id='logo-desktop' src="/images/logo-zport-branca-3x.png" alt="">
        </a>

        <div class="nav-control">
            <div class="hamburger" id="botao-hamburger">
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
                            <!-- <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                                <i class="mdi mdi-magnify"></i>
                            </span>
                            <div class="dropdown-menu p-0 m-0">
                                <form>
                                    <input class="form-control" type="search" placeholder="Pesquisar" aria-label="Search">
                                </form>
                            </div> -->
                        </div>
                    </div>

                    <ul class="navbar-nav header-right">
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                <i class="mdi mdi-account"></i>
                                <!-- <div class="pulse-css"></div> -->
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
                                <!-- <a href="./app-profile.html" class="dropdown-item">
                                    <i class="icon-user"></i>
                                    <span class="ml-2">Profile</span>
                                </a>
                                <a href="./email-inbox.html" class="dropdown-item">
                                    <i class="icon-envelope-open"></i>
                                    <span class="ml-2">Inbox</span>
                                </a> -->
                                <a href="#" class="dropdown-item" onclick="logoutConfirmation()">
                                    <i class="mdi mdi-exit-to-app"></i>
                                    <span class="ml-2">Sair</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>

<script src="<?php echo $urlBase; ?>js/main.js"></script>