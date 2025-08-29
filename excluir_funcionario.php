<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil']!= 1) {
    echo"<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa as variaveis 
$funcionario = null;

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Se um id for passado via GET, excluir o usuário 
if (isset($_GET['id_funcionario']) && is_numeric($_GET['id_funcionario'])){
    $id_funcionario = $_GET['id_funcionario'];
    
    // excluir o usuario do banco de dados 
    $sql = "DELETE FROM funcionario WHERE id_funcionario = :id_funcionario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);

    if ($stmt->execute()){
        echo "<script>alert('Usuário excluído com sucesso!');window.location.href='excluir_funcionario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir usuário.');</script>";
    }
}
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
    <title>Excluir usuário</title>
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

    <center><h2> Excluir Funcionário</h2> </center>
    <?php if(!empty($funcionarios)):?>
        <table border = "1" class ="table table-striped">
            <tr> 
            <th> ID </th>
                <th> Nome </th>
                <th> Telefone </th>
                <th> Email </th>
                <th> Ações </th>
            </tr>
        
            <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                <td> <?=htmlspecialchars($funcionario['id_funcionario']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['telefone']) ?></td>
                    <td> <?=htmlspecialchars($funcionario['email']) ?></td>
                  
                
                    <td>
                        <a href="excluir_funcionario.php?id_funcionario=<?= htmlspecialchars($funcionario['id_funcionario']) ?>"onclick= "return confirm('Tem certeza que deseja excluir este usuário?')" class="btn btn-primary">
                            Excluir 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>     
        </table>
            <?php else: ?>
            <p> Nenhum usuário encontrado.</p>
    <?php endif; ?>
    <center> <a href="principal.php" class="btn btn-primary">Voltar</a></center> 
</body>
</html>