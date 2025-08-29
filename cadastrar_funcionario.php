<?php
session_start();

require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSÃO
// SUPONDO QUE O PERFIL '1' SEJA O 'ADM'
if ($_SESSION['perfil'] != 1) {
    echo "Acesso negado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_funcionario'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $query = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email) VALUES (:nome_funcionario, :endereco, :telefone, :email)";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(":nome_funcionario", $nome);
    $stmt->bindParam(":endereco", $endereco);
    $stmt->bindParam(":telefone", $telefone);
    $stmt->bindParam(":email", $email);

    try {
        $stmt->execute();
        echo "<script> alert('Usuário cadastrado com sucesso!'); </script>";
    } catch (PDOException $e) {
        echo "<script> alert('Erro ao cadastrar o usuário! Verifique as informações inseridas.'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Usuário</title>

    <link rel="stylesheet" href="styles.css">
    <script src="validacoes.js"></script>
</head>

<body>

    <h2>Cadastro Funcionario</h2>

    <form action="cadastrar_funcionario.php" method="POST">
        <label for="nome_funcionario">Nome:</label>
        <input type="text" name="nome_funcionario" id="nome_funcionario" required
            onkeypress="mascara(this, somentetexto)">

        <label for="senha">Endereco:</label>
        <input type="text" name="endereco" id="endereco" required>

        <label for="senha">Telefone:</label>
        <input type="text" name="telefone" id="telefone" required>

        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>

        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>
    
</body>

</html>
