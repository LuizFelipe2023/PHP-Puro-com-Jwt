<?php
require_once('./vendor/autoload.php');
require_once('conexao.php');
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');

use Firebase\JWT\JWT;

// Exibir todos os erros do PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        echo "Preencha todos os campos!";
        exit;
    }

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    // Verifica se o usuário existe com o e-mail fornecido
    $stmt1 = $conn->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt1->bindParam(1, $email, PDO::PARAM_STR);
    $stmt1->execute();
    $user = $stmt1->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Usuário não existe ou não foi encontrado";
        exit;
    }

    // Verifica se a senha está correta
    if (!password_verify($senha, $user['senha'])) {
        echo "Credenciais inválidas";
        exit;
    }

    $token = generateJWT($email);
    // Atualiza o token no banco de dados
    $stmt2 = $conn->prepare('UPDATE usuarios SET token = ? WHERE email = ?');
    $stmt2->bindParam(1, $token, PDO::PARAM_STR);
    $stmt2->bindParam(2, $email, PDO::PARAM_STR);
    $stmt2->execute();

    // Armazena o token na sessão
    $_SESSION['token'] = $token;

    echo "Login bem-sucedido!";
    header('location: home.php');
    exit();
}

function generateJWT($email)
{
    // Gera o token JWT com base no e-mail do usuário
    $payload = [
        'exp' => time() + 3600, // Token expira em 1 hora
        'iat' => time(),
        'email' => $email
    ];
    $secretKey = 'dfbgufjf0487r427t5u84hg4874328ut4875447ut8rghhf78fhifdvh7re98yt';
    $token = JWT::encode($payload, $secretKey, 'HS512');
    return $token;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="login.php" method="post">
          <label for="email">Email:</label>
          <input type="email" name="email" id="email">
          <label for="senha">Senha:</label>
          <input type="password" name="senha" id="senha">
          <button type="submit">Enviar</button>
    </form>
</body>
</html>
