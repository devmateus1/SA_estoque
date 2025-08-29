<?php
    session_start();
    require_once 'conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] !=2) {
        echo "<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
        exit();
    }

// Inicializa a variável para evitar erros 
$funcionarios = [];

// Se o formulário for enviado, busca o usuário pelo id ou nome.

if ($_SERVER["REQUEST_METHOD"] ==  "POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    // Verifica se a busca é um número (id) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM funcionario WHERE nome_funcionario like :busca ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";       
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$funcionarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Obtendo o nome do perfil do usuario logado 
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
    <title>Buscar Funcionario</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
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


     <center> <h2> Lista de Funcionarios </h2></center> 
    <!-- Formulário para buscar usuário -->
     <form action="buscar_funcionario.php" method="POST">
        <label for="busca"> Digite o ID ou Nome (opcional):</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button> 
    </form>

    <?php if(!empty($funcionarios)):?>
       <center>  <table border = "1" class ="table table-striped">
            <tr>
                <th> ID </th>
                <th> Nome </th>
                <th> Endereço </th>
                <th> Telefone </th>
                <th> Email </th>
                <th> Ações </th>
            </tr>

            <?php foreach($funcionarios as $funcionario): ?>
                <tr>
                    <td> <?=htmlspecialchars($funcionario['id_funcionario']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['endereco']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['telefone']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['email']) ?></td>
                    <td>
                        <a href="alterar_funcionario.php?id= <?=htmlspecialchars($funcionario['id_funcionario'])?>" class="btn btn-primary"> Alterar</a>

                        <a href="excluir_funcionario.php?id= <?=htmlspecialchars($funcionario['id_funcionario'])?>"onclick="return confirm
                        ('Tem certeza que deseja excluir este funcionario?')" class="btn btn-primary"> Excluir</a>
                    </td>
                </tr>
             <?php endforeach; ?>   
        </table> </center>
    <?php else: ?>
        <p>Nenhum funcionario encontrado.</p>
    <?php endif; ?> 
    
    <center> <a href="principal.php" class="btn btn-primary">Voltar</a></center> 
</body>
</html>