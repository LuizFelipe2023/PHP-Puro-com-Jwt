<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once('conexao.php');

use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
getEnv('my_secret');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        $errors[] = "Por favor, preencha todos os campos!";
    }

    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Digite um email válido!";
    }

    $senha = $_POST['senha'];
    if (strlen($senha) < 4) {
        $errors[] = "A senha deve ter pelo menos 4 caracteres!";
    }

    if (empty($errors)) {
        $pepper = "[e+n.bhJ;f,#(X9tsPq94^[3}J('7vW";
        $senhacomPepper = hash_hmac('sha256', $senha, $pepper);
        $senhahash = password_hash($senhacomPepper, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (email, senha, token) VALUES (?, ?, ?)");
        $payload = [
            'exp' => time() + 360,
            'iat' => time(),
            'email' => $email
        ];
        $token = JWT::encode($payload, $_ENV['my_secret'], 'HS512');
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->bindParam(2, $senhahash, PDO::PARAM_STR);
        $stmt->bindParam(3, $token, PDO::PARAM_STR);
        $stmt->execute();

        if (!$stmt->rowCount()) {
            $errors[] = "Erro ao cadastrar o usuário!";
        } else {
            echo "Usuário cadastrado com sucesso!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
    <!-- Adicionando Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container pt-5">
        <form action="cadastrar.php" method="post">
            <h2 class="text-center">Cadastro</h2>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email">
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" name="senha" id="senha">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Enviar</button>
        </form>
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <a href="login.php" class="btn btn-primary text-center mt-4">Ja Possui Conta? Pressione Aqui para ir para tela de login</a>
    </div>
    
</body>

</html>