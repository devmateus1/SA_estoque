<?php
session_start();
require_once 'conexao.php';


if ($_SESSION['perfil']!= 1) {
    echo"<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}


$funcionario = null;


if ($_SERVER["REQUEST_METHOD"] ==  "POST"){

   if (!empty($_POST['busca_funcionario'])){
    $busca = trim($_POST['busca_funcionario']);


    if (is_numeric($busca)) {
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM funcionario WHERE nome_funcionario like :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if(!$funcionario) {
        echo "<script>alert('Usuário não encontrado.');</script>";
    }
}
}

$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

$permissoes = [
    1=> ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

    2=> ["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php", "alterar_fornecedor.php"]],
        
    3=> ["Cadastrar"=>[ "cadastro_fornecedor.php", "cadastro_produto.php"],
        "Buscar"=>[ "buscar_cliente.php", "buscar_fornecedor.php", "buscar_funcionario.php"],
        "Alterar"=>[ "alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],
    
    4=> ["Cadastrar"=>[ "cadastro_cliente.php"],
        "Alterar"=>[ "alterar_cliente.php"]]
];

$opcoes_menu = $permissoes[$id_perfil]; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
    
    <script src="scripts.js"></script>
</head>
<body>
<nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
            <li class="dropdown">
                    <a href="#"><?php echo $categoria; ?></a>
                    <ul class="dropdown-menu">
                    <?php foreach($arquivos as $arquivo):?>
                        <li>
                            <a href="<?=$arquivo ?>"> <?= ucfirst(str_replace("_", " ",basename ($arquivo, ".php")))?></a>
                        </li>
                        <?php endforeach; ?> 
                    </ul>
            </li>
            <?php endforeach; ?>
        </ul>
     </nav>
    <h2> Lista de Funcionarios </h2>
      
        <form action="alterar_funcionario.php" method="POST">
            <label for="busca_funcionario"> Digite o ID ou Nome do funcionário:</label>
            <input type="text" id="busca_funcionario" name="busca_funcionario" required onkeyup="buscarSugestoesFunc()">

            <div id ="sugestoes"></div>
            <button type="submit" class="btn btn-primary"s>Buscar</button> 
         </form>

    <?php if($funcionario): ?>
        <form action="processa_alteracao_funcionario.php" method="POST">
            <input type="hidden" name="id_funcionario" value="<?=htmlspecialchars($funcionario['id_funcionario'])?>">

            <label for="nome_funcionario"> Nome:</label>
            <input type="text" id="nome_funcionario" name="nome_funcionario" value="<?=htmlspecialchars($funcionario['nome_funcionario'])?>" required>

            <label for="endereco"> Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?=htmlspecialchars($funcionario['endereco'])?>" required>

            <label for="telefone"> Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?=htmlspecialchars($funcionario['telefone'])?>" required>

            <label for="email"> Email:</label>
            <input type="text" id="email" name="email" value="<?=htmlspecialchars($funcionario['email'])?>" required>
          
            <?php if ($_SESSION['perfil'] === 1):  ?>
                <label for="nova_senha"> Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha">
                <?php endif; ?>

                <button type="submit" class="btn btn-primary" > Alterar</button>
                <button type="reset" class="btn btn-primary"> Cancelar</button>
        </form>     
        <?php endif; ?>
        <center> <a href="principal.php" class="btn btn-primary">Voltar</a></center> 
</body>
</html>