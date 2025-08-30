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

    $sql = "INSERT INTO produto (titulo, autor, editora, ano_publicacao, categoria, data_cadastro) VALUES (:titulo, :autor, :autor, :ano_publicacao, :categoria, :data_cadastro)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':editora', $editora);
    $stmt->bindParam(':ano_publicacao', $ano_publicacao);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':data_cadastro', $data_cadastro);

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
                <label for="titulo">Tituo do livro:</label>
                <input type="text" id="titulo" name="titulo" required><br><br>
                
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" required><br><br>
                
                <label for="editora">Editora:</label>   
                <input type="text" id="editora" name="editora" required><br><br>
                
                <label for="ano_publicacao">Ano Da Publicacao:</label>
                <input type="data" id="ano_publicacao" name="ano_publicacao" required><br><br>

                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" required><br><br>

                <label for="data_cadastro">Data Cadastro:</label>
                <input type="data" id="data_cadastro" name="data_cadastro" required><br><br>
                
                <button type="submit">Salvar</button>
                <button type="reset">Cancelar</button>
            </form>
            
            <center><a href="principal.php">Voltar</a></center>

            <script src="validacao.js"></script>
        </body>

</html>