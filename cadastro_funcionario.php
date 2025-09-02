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
    1=> ["Cadastrar"=>["cadastro_usuario.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php",  "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php",  "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            min-height: 100vh;
            color: #333;
        }

        nav {
            background: rgba(30, 58, 138, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .dropdown {
            position: relative;
        }

        .dropdown > a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: block;
            font-weight: 500;
        }

        .dropdown > a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(30, 58, 138, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            list-style: none;
            padding: 0.5rem 0;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: block;
            transition: all 0.2s ease;
            border-radius: 6px;
            margin: 0 0.5rem;
        }

        .dropdown-menu li a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        h2 {
            text-align: center;
            color: white;
            margin: 2rem 0;
            font-size: 2.5rem;
            font-weight: 300;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        form {
            max-width: 600px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1e3a8a;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        button {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.5rem 0.5rem 0.5rem 0;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
        }

        button[type="reset"] {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        button[type="reset"]:hover {
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
            border: none;
            margin: 1rem 0;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
            color: white;
            text-decoration: none;
        }

        center {
            text-align: center;
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 1rem;
            }
            
            form {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }
            
            h2 {
                font-size: 2rem;
            }
        }
    </style>
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
                        <a href="<?php echo htmlspecialchars($arquivo); ?>"> <?= ucfirst(str_replace("_", " ",basename ($arquivo, ".php")))?></a>
                    </li>
                    <?php endforeach; ?> 
                </ul>
        </li>
        <?php endforeach; ?>
    </ul>
 </nav>

 <h2>Cadastrar Funcionário</h2>

 <form action="cadastro_funcionario.php" method="POST">
    <label for="nome_funcionario">Nome do funcionário:</label>
    <input type="text" id="nome_funcionario" name="nome_funcionario" required onkeypress="mascara(this.nome)">

    <label for="endereco">Endereço:</label>
    <input type="text" name="endereco" id="endereco" required>

    <label for="telefone">Telefone:</label>
    <input type="text" name="telefone" id="telefone" required onkeypress="mascara(this.telefone1)" maxlength="15">

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <button type="submit">Cadastrar</button>
    <button type="reset">Cancelar</button>
    
</form>

<center><a href="principal.php" class="btn-primary">Voltar</a></center> 

</body>
</html>
