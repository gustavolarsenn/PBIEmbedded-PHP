<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Zport</title>
    <!-- Favicon icon -->
    <link rel="icon" type="img/z.png" sizes="16x16" href="./images/favicon.png">
    <link href="./css/style.css" rel="stylesheet">

</head>

<style>
        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
</style>

<body class="h-100">

    <div class="authincation h-100">
        <div class="container-fluid h-100">
		<video autoplay muted loop>
        <source src="background-video.mp4" type="video/mp4">
      
    </video>
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
								<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
								
								<div class="auth-form">
        
									<h2>Login</h2>

									<?php
									session_start();

									// Verificar se o usuário já está logado, redirecionar para a página principal
									if (isset($_SESSION['user_id'])) {
										header("Location: index.php");
										exit();
									}

									// Verificar se o formulário foi enviado
									if ($_SERVER["REQUEST_METHOD"] == "POST") {
										// Conectar ao banco de dados (substitua pelos seus próprios detalhes)
									$host = "localhost";
									$usuario = "root";
									$senha = "4t4lyb4h";
									$banco = "sistema_navio";

									$conn = new mysqli($host, $usuario, $senha, $banco);

										// Verificar a conexão
										if ($conn->connect_error) {
											die("Falha na conexão: " . $conn->connect_error);
										}

										// Obter dados do formulário
										$username = $_POST["username"];
										$password = md5($_POST["password"]);

										// Consulta SQL para verificar as credenciais
										$sql = "SELECT id, usuario, senha, emailUsuario FROM usuarios WHERE usuario = '$username' OR emailUsuario = '$username' AND senha = '$password'";
										$result = $conn->query($sql);

										// Verificar se as credenciais são válidas
										if ($result->num_rows > 0) {
											$row = $result->fetch_assoc();
											$_SESSION['user_id'] = $row['id'];
											header("Location: index.php");
											exit();
										} else {
											echo "<p>Credenciais inválidas. Tente novamente.</p>";
										}

										// Fechar conexão
										$conn->close();
									}
									?>

									<div class="form-group">
										
										<label><strong>Email</strong></label>
										<input class="form-control" type="email" name="username" value="hello@example.com.br" required>
									</div>
									
									<div class="form-group">
										<label for="password">Senha:</label>
										<input class="form-control" type="password" name="password" required>
									</div>
										
									</div>
									<!--<div class="form-row d-flex justify-content-between mt-4 mb-2">-->
                                        <div class="form-group">
                                            <div class="form-check ml-2">
                                                <input class="form-check-input" type="checkbox" id="basic_checkbox_1">
                                                <label class="form-check-label" for="basic_checkbox_1">Lembre-me</label>
                                            </div>
                                        </div>
										<div class="form-group">
											<a href="page-forgot-password.html">Esqueceu a senha?</a>
										</div>
                                    <!--</div>-->
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                                    </div>
								</form>
                                
								<!--<div class="auth-form">
                                    <h4 class="text-center mb-4">Sign in your account</h4>
                                    <form action="index.html">
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" class="form-control" value="hello@example.com">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Password</strong></label>
                                            <input type="password" class="form-control" value="Password">
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <div class="form-check ml-2">
                                                    <input class="form-check-input" type="checkbox" id="basic_checkbox_1">
                                                    <label class="form-check-label" for="basic_checkbox_1">Remember me</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <a href="page-forgot-password.html">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Sign me in</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p>Don't have an account? <a class="text-primary" href="./page-register.html">Sign up</a></p>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
    <script src="./js/quixnav-init.js"></script>
    <script src="./js/custom.min.js"></script>

</body>

</html>