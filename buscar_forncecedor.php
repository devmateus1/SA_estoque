<?php

session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] !=2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}
// INICIALIZA A VARIAVEL PARA EVITAR ERROS

$usuarios = [];

// SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO ID OU NOME

if ($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['busca'])){
 $busca = trim($_POST['busca']);

 // VERIFICA SE A BUSCA É UM NUMERO (ID) OU UM NOME

 if(is_numeric($busca)){
    $sql =  "SELECT * FROM fornecedor WHERE id_fornecedor = :busca ORDER BY nome_fornecedor ASC";
    $stmt =$pdo->prepare($sql);
    $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
 } else {
    $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome ORDER BY nome_fornecedor ASC";
    $stmt =$pdo->prepare($sql);
    $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
 }
} else{
    $sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
    $stmt =$pdo->prepare($sql);
}
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

    
$permissoes = [
    1=> ["Cadastrar"=>["cadastro_usuario.php",  "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"], // Admin
        "Buscar"=>["buscar_usuario.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php",  "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

    2=> ["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"], // Funcionario
        "Alterar"=>["alterar_cliente.php", "alterar_fornecedor.php"]],
        
    3=> ["Cadastrar"=>[ "cadastro_fornecedor.php", "cadastro_produto.php"],         // Gerente
        "Buscar"=>[ "buscar_cliente.php", "buscar_fornecedor.php", "buscar_funcionario.php"],
        "Alterar"=>[ "alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],
    
    4=> ["Cadastrar"=>[ "cadastro_cliente.php"],   // Cliente
        "Alterar"=>[ "alterar_cliente.php"]]
];    

$opcoes_menu = $permissoes[$id_perfil];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Fornecedor</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>
<nav style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <h1 style="color: white; margin: 0; font-size: 1.5rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                📚 Sistema de Biblioteca
            </h1>
            
            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div style="position: relative; display: inline-block;">
                    <button onclick="toggleDropdown()" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        📋 Menu ▼
                    </button>
                    <div id="dropdown" style="display: none; position: absolute; right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); min-width: 200px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border-radius: 12px; z-index: 1000; border: 1px solid rgba(255, 255, 255, 0.2); margin-top: 0.5rem;">
                        <a href="cadastro_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">📚 Cadastrar Livro</a>
                        <a href="buscar_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋 Listar Livros</a>
                        <a href="alterar_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️ Alterar Livro</a>
                        <a href="excluir_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🗑️ Excluir Livro</a>
                        <a href="principal.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠 Painel Principal</a>
                    </div>
                </div>
    <center><h2>Lista de Fornecedores</h2></center>

    <!-- FORMULARIO PARA BUSCAR FORNECEDOR -->

    <form action="buscar_fornecedor.php" method="POST">
        <label for="busca">Digite o ID ou NOME do fornecedor(opcional)</label>
        <input type="text" id="busca" name="busca">
        <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>

    <?php if(!empty($usuarios)):?>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <center><table border="1" class ="table table-striped"> 
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Contato</th>
                <th>Ações</th>
            </tr>

            <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?=htmlspecialchars($usuario['id_fornecedor']) ?></td>
                    <td><?=htmlspecialchars($usuario['nome_fornecedor']) ?></td> 
                    <td><?=htmlspecialchars($usuario['endereco']) ?></td>
                    <td><?=htmlspecialchars($usuario['telefone']) ?></td>
                    <td><?=htmlspecialchars($usuario['email']) ?></td>
                    <td><?=htmlspecialchars($usuario['contato']) ?></td>
                    <td>
                        <a class = "btn btn-warning"href="alterar_fornecedor.php?id=<?=htmlspecialchars($usuario['id_fornecedor'])?>">Alterar</a>
                        <a class = "btn btn-danger" href="excluir_fornecedor.php?id=<?=htmlspecialchars($usuario['id_fornecedor'])?>"onclick="return confirm('Tem certeza que deseja excluir esse fornecedor?')">Excluir</a>
                    </td> 
                </tr>
            <?php endforeach; ?>
        </table></center>
    <?php else: ?>
        <p> Nenhum fornecedor encontrado.</p>
    <?php endif; ?>
    <br>
    <center><a href="principal.php" class="btn btn-primary" >Voltar</a></center>

</body>
</html>