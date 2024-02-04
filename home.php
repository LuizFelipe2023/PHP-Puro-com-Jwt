<?php
require_once('conexao.php');
session_start();

// Verifica se o usuário está autenticado
if(!isset($_SESSION['token'])){
    header('location: login.php'); // Redireciona para a página de login se não estiver autenticado
    exit();
}

// Obtém o token de autenticação da sessão
$token = $_SESSION['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h2>Você está autenticado!</h2>
    <p>Seu token de autenticação é: <?php echo $token; ?></p>
</body>
</html>
