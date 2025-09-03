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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Funcionário</title>
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

        /* Navigation styling with blur effect */
        nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
            flex-wrap: wrap;
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
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            list-style: none;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li a {
            color: #1e3a8a;
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 0.25rem;
        }

        .dropdown-menu li a:hover {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            transform: translateX(5px);
        }

        /* Main content container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h2 {
            text-align: center;
            color: white;
            margin-bottom: 2rem;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Table styling */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(30, 58, 138, 0.1);
        }

        th {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        tr:hover {
            background: rgba(30, 58, 138, 0.05);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        /* Button styling */
        .btn {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            cursor: pointer;
        }

        .btn:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4);
        }

        .btn-back {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            margin-top: 1rem;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.4);
        }

        .back-container {
            text-align: center;
            margin-top: 2rem;
        }

        /* No data message styling */
        .no-data {
            text-align: center;
            color: white;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 12px;
            margin: 2rem auto;
            max-width: 500px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 0.5rem;
            }

            .dropdown-menu {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                margin-top: 0.5rem;
            }

            .container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            th, td {
                padding: 0.5rem;
                font-size: 0.875rem;
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
                            <a href="<?php echo htmlspecialchars($arquivo); ?>"> 
                                <?= ucfirst(str_replace("_", " ",basename ($arquivo, ".php")))?>
                            </a>
                        </li>
                    <?php endforeach; ?> 
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="container">
        <h2>Excluir Funcionário</h2>
        
        <?php if(!empty($funcionarios)):?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr> 
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?=htmlspecialchars($funcionario['id_funcionario']) ?></td>
                                <td><?=htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                                <td><?=htmlspecialchars($funcionario['telefone']) ?></td>
                                <td><?=htmlspecialchars($funcionario['email']) ?></td>
                                <td>
                                    <a href="excluir_funcionario.php?id_funcionario=<?= htmlspecialchars($funcionario['id_funcionario']) ?>"
                                       onclick="return confirm('Tem certeza que deseja excluir este funcionário?')" 
                                       class="btn">
                                        Excluir 
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>     
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>Nenhum funcionário encontrado.</p>
            </div>
        <?php endif; ?>
        
        <div class="back-container">
            <a href="principal.php" class="btn btn-back">Voltar</a>
        </div>
    </div>
</body>
</html>
