<?php
session_start();
use Firebase\JWT\JWT;
require_once(__DIR__."/../vendor/autoload.php");
require_once('conexao.php');
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
getEnv('my_secret');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');

if($_SERVER['REQUEST_METHOD']=='POST'){
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        echo "Preencha todos os campos!";
    } else {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $stmt1 = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $stmt1->bindParam(1, $email, PDO::PARAM_STR);
        $stmt1->execute();
        $user = $stmt1->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            echo "Usuário não existe ou não foi encontrado";
            exit;
        }
        $token = generateJWT($email);
        $stmt2 = $conn->prepare('UPDATE users SET token = ? WHERE email = ?');
        $stmt2->bindParam(1, $token, PDO::PARAM_STR);
        $stmt2->bindParam(2, $email, PDO::PARAM_STR);
        $stmt2->execute();

        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        echo "Login bem-sucedido!";
        header('Location: home.php');
        exit();
    }
}

function generateJWT($email)
{
    $payload = [
        'exp' => time() + 360, 
        'iat' => time(),
        'email' => $email
    ];
    $token = JWT::encode($payload, $_ENV['my_secret'], 'HS512');
    return $token;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Adicionando Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/./public/style.css">
</head>
<body>
    <div class="container pt-5">
        <div class="login-form">
            <h2 class="text-center">Login</h2>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" class="form-control" name="senha" id="senha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enviar</button>
            </form>
        
        </div>
        <a href="cadastrar.php" class="btn btn-primary text-center mt-4">Não tem Conta? Clique aqui para fazer uma</a>
    </div>
</body>
</html>
