<?php
require_once __DIR__ . '\\..\\..\\config.php'; 

require_once CAMINHO_BASE . '\\SessionManager.php';

SessionManager::checarSessao();
SessionManager::checarCsrfToken();

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

    <link href="<?php echo $urlBase; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/erro.css" rel="stylesheet">
</head>

<body>
    <div class="error-container">
        <img class="logo-compact" src="/img/zport-logo-3x.png" width="30%">
        <br>
        <div>
            <h1>Erro:</h1>
            <p>Não foi possível conectar com <strong>banco de dados</strong>. Favor entrar em contato com <a href="mailto:pctvzport@gmail.com">suporte</a>.</p>
            <br>
            <a href="/views/index.php">Voltar para página inicial</a>
        </div>
    </div>
</body>

</html>