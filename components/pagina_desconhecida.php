<?php
$urlBase = '/'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Página desconhecida</title>

    <link rel="icon" type="image/png" href="<?php echo $urlBase;?>img/icone.png">

    <link href="<?php echo $urlBase; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $urlBase; ?>css/erro.css" rel="stylesheet">
</head>

<body>
    <div class="error-container">
        <img class="logo-compact" src="/img/zport-logo-3x.png" width="30%">
        <br>
        <div>
            <h1>Página desconhecida!</h1>
            <p>Nenhuma página encontrada para o link <strong><?php echo $_SERVER['REQUEST_URI'] ?></strong>.
            <br>
            <a href="/index.php">Voltar para página inicial</a>
        </div>
    </div>
</body>

</html>