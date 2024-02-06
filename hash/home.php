<?php
session_start();
require_once(__DIR__."/../vendor/autoload.php");
require_once('conexao.php');
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
getEnv('my_secret');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');

if(!isset($_SESSION['token']) || !isset($_SESSION['email'])) {
   header('Location: login.php');
   exit();
}

$token = $_SESSION['token'];
$email = $_SESSION['email'];

if(!Authenticate($token, $email)) {
   header('Location: login.php');
   exit();
}

// Função para autenticar o token e o email
function Authenticate($token, $email) {
    try {
        $decoded = JWT::decode($token, new Key($_ENV['my_secret'], 'HS512'));
        $payload = (array)$decoded;
        return ($payload['email'] === $email);
    } catch (Exception $e) {
        throw new Exception('Erro ao autenticar o token: '.$e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Adicionando Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Você está autenticado!</h2>
        <p>Seu token de autenticação é: <?php echo $token; ?></p><br><br>
    </div>
</body>
</html>
