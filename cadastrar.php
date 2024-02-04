<?php
require_once('./vendor/autoload.php');
require_once('conexao.php');
use Firebase\JWT\JWT;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        $errors[] = "Preencha todos os campos!";
    } else {
        $email = $_POST['email'];
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Digite um email válido!";
        }

        $senha = $_POST['senha'];
        if (strlen($senha) < 4) {
            $errors[] = "Digite uma senha com pelo menos 4 caracteres!";
        }
    }

    if (empty($errors)) {
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios (email, senha, token) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->bindParam(2, $senhaHash, PDO::PARAM_STR);

        $payload = [
            'exp' => time() + 3600,
            'iat' => time(),
            'email' => $email
        ];
        $token = JWT::encode($payload, 'dfbgufjf0487r427t5u84hg4874328ut4875447ut8rghhf78fhifdvh7re98yt', 'HS512');
        $stmt->bindParam(3, $token, PDO::PARAM_STR);

        $result = $stmt->execute();
        if (!$result) {
            $errors[] = "Erro ao cadastrar um novo usuário";
        } else {
            echo "Usuário cadastrado com sucesso";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="cadastrar.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email">
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha">
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
