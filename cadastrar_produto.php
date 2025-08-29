<?php
session_start();
require_once 'conexao.php';
require_once 'funcao_dropdown.php';

// Verifica se o usuário tem permissão para acessar a página
// Supondo que o perfil 1 seja o administrador
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado. Você não tem permissão para acessar esta página.'); window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $qtde = $_POST['qtde'];
    $valor = $_POST['valor'];

    $sql = "INSERT INTO produto (nome_prod, descricao, qtde, valor_unit) VALUES (:nome, :descricao, :qtde, :valor)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':qtde', $qtde);
    $stmt->bindParam(':valor', $valor);

    if ($stmt->execute()) {
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar produto. Tente novamente.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <h2 align="center">Cadastrar Produto</h2>
            <form action="cadastro_produto.php" method="POST" align="center">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required><br><br>
                
                <label for="descricao">Descricao:</label>
                <input type="text" id="descricao" name="descricao" required><br><br>
                
                <label for="qtde">Quantidade:</label>   
                <input type="number" id="qtde" name="qtde" required onkeypress="mascara(this,valida_number)"><br><br>
                
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="any" required><br><br>
                
                <button type="submit">Salvar</button>
                <button type="reset">Cancelar</button>
            </form>
            
            <center><a href="principal.php">Voltar</a></center>

            <script src="validacao.js"></script>
        </body>

</html>