<?php
require_once __DIR__ . '/../../utils/config/config.php'; 

$urlBase = '/'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sessão Expirada</title>

    <link rel="icon" type="image/png" href="<?php echo $urlBase;?>utils/img/icone.png">

    <link href="<?php echo $urlBase; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/erro.css" rel="stylesheet">
</head>

<body>
    <div class="error-container">
        <img class="logo-compact" src="/utils/img/zport-logo-3x.png" width="30%">
        <br>
        <div>
            <h1>Sessão expirada.</h1>
            <p>Faça login novamente.</p>
            <br>
            <a href="/login.php">Ir para página de Login</a>
        </div>
    </div>
</body>

</html>