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

        // Verify password
        if (password_verify($senha, $user['senha'])) {
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
        } else {
            echo "Senha incorreta!";
        }
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
