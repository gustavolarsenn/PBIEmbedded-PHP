<?php
require_once __DIR__ . '/../../config/config.php'; 

$urlBase = '/'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Erro</title>

    <link rel="icon" type="image/png" href="<?php echo $urlBase;?>config/img/icone.png">

    <link href="<?php echo $urlBase; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/erro.css" rel="stylesheet">
</head>

<body>
    <div class="error-container">
        <img class="logo-compact" src="/config/img/zport-logo-3x.png" width="30%">
        <br>
        <div>
            <h1>Erro:</h1>
            <p>Não foi possível conectar com <strong>banco de dados</strong>. Favor entrar em contato com o <a href="mailto:pc-tv@zport.com.br">suporte</a>.</p>
            <br>
            <a href="/index.php">Voltar para página inicial</a>
        </div>
    </div>
</body>

</html>