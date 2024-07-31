<?php

require_once __DIR__ . '\\..\\config.php';

require_once CAMINHO_BASE . '\\models\\PBI\\RelatorioPBI.php';

if($_POST['action'] === 'gerarRelatorioPBI'){
    $actualLink = $_POST['reportName'];
    $relatoriPBI = new RelatorioPBI();
    echo $relatoriPBI->gerarRelatorioPBI($actualLink);
}