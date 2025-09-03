<?php
session_start();
require_once 'conexao.php';

// verifica se o usuario tem permissao 
if($_SESSION['perfil']!= 1) {
    echo "Acesso negado. ";
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
    1=> ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil" ,  "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"], // Admin
        "Buscar"=>["buscar_usuario.php", "buscar_perfil" , "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_perfil" ,"alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_perfil" , "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

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


// OBTENDO AS OPÇÕS DISPONIVEIS PARA O PERFIL LOGADO
$opcoes_menu = $permissoes[$id_perfil];

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $contato = $_POST['contato'];
    $sql = "INSERT INTO fornecedor (nome_fornecedor, endereco, telefone, email, contato) VALUES (:nome_fornecedor, :endereco, :telefone, :email, :contato)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contato', $contato);

    if($stmt->execute()) {
        echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar Fornecedor.');</script>";
    }
};
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Fornecedor</title>
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

        /* Navigation Styles */
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
            padding: 0 1rem;
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
            background: rgba(30, 58, 138, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            list-style: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 0.25rem;
        }

        .dropdown-menu li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        /* Main Content */
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        input[type="text"],
        input[type="email"] {
            padding: 1rem;
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

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        button {
            flex: 1;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
        }

        button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.3);
        }

        button[type="reset"] {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
        }

        button[type="reset"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .back-link a:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(107, 114, 128, 0.3);
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 0.5rem;
            }

            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
            <li class="dropdown">
                <a href="#"><?= $categoria ?></a>
                <ul class="dropdown-menu">
                    <?php foreach($arquivos as $arquivo): ?>
                    <li>
                        <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                    </li>
                        <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="container">
        <h2>Cadastro de Fornecedor</h2>
        <form method="POST" action="cadastro_fornecedor.php">
            <div>
                <label for="nome_fornecedor">Nome Fornecedor:</label>
                <input type="text" id="nome_fornecedor" name="nome_fornecedor" required onkeyup="validarNomeFornecedor()">
            </div>
            
            <div>
                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" required>
            </div>
            
            <div>
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required onkeyup="validarTelefone()">
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="contato">Contato:</label>
                <input type="text" id="contato" name="contato" required>
            </div>

            <div class="button-group">
                <button type="submit">Cadastrar</button>
                <button type="reset">Cancelar</button>
            </div>
        </form>

        <div class="back-link">
            <a href="principal.php">Voltar</a>
        </div>
    </div>

    <script>
        function validarNomeFornecedor() {
            // Adicione sua lógica de validação aqui
        }

        function validarTelefone() {
            // Adicione sua lógica de validação aqui
        }
    </script>
</body>
</html>
