<?php 
session_start();
require_once 'conexao.php';


if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado";
    exit();
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

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome_funcionario = $_POST['nome_funcionario'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email ) VALUES (:nome_funcionario, :endereco, :telefone, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_funcionario', $nome_funcionario);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);

    if ( $stmt->execute()){
        echo "<script>alert('Funcionário cadastrado com sucesso!');</script>";
    }else {
        echo "<script>alert('Erro ao cadastrar Funcionário.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionario</title>
    <link rel="stylesheet" href="styles.css">
    <script src="mascaras.js"></script>
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
     <br>

     <h2> <center>Cadastrar Funcionário </center></h2>

     <form action="cadastro_funcionario.php" method="POST">
        <label for="nome_funcionario"> Nome do funcionário: </label>
        <input type="text" id="nome_funcionario" name="nome_funcionario" required onkeypress="mascara(this.nome)">

        <label for="endereco"> Endereço: </label>
        <input type="text" name="endereco" id="endereco" placeholder="**/**/****" required>

        <label for="telefone"> Telefone: </label>
        <input type="text" name="telefone" id="telefone" required onkeypress="mascara(this.telefone1)" maxlenght="15">

        <label for="email"> Email: </label>
        <input type="email" name="email" id="email" required>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
        <br>
        <button type="reset" class="btn btn-primary">Cancelar</button>
        
    </form>

    <center> <a href="principal.php" class="btn btn-primary">Voltar</a></center> 
    
</body>
</html>
















?>