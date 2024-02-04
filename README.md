# Autenticação com JWT em PHP Puro

Este repositório contém um exemplo simples de autenticação com JSON Web Tokens (JWT) em PHP. Ele consiste em três páginas:

1. **login.php**: Página de login onde os usuários podem inserir suas credenciais.
2. **cadastrar.php**: Página de cadastro onde os usuários podem registrar novas contas.
3. **home.php**: Página inicial acessível apenas para usuários autenticados, exibindo uma mensagem de boas-vindas e o token de autenticação.

## Pré-requisitos

Antes de executar este exemplo, certifique-se de ter o seguinte:

- PHP instalado em seu ambiente de desenvolvimento.
- Um servidor web configurado (por exemplo, Apache, Nginx).
- Extensão PDO habilitada para interagir com o banco de dados.
- A biblioteca Firebase JWT PHP para lidar com JWT. Esta biblioteca está incluída no arquivo `composer.json` e pode ser instalada executando `composer install`.

## Configuração do Banco de Dados

O exemplo usa uma tabela de usuários para armazenar informações de autenticação. A estrutura da tabela é a seguinte:

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    token TEXT
);
