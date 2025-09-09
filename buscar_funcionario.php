<?php
session_start();
require_once 'conexao.php';

// Verifica permissão (apenas Admin e Gerente podem buscar)
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso negado.'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variáveis
$funcionarios = [];

// Obtém o nome do perfil do usuário logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'] ?? 'Usuário';

// Definição de permissões
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar" => ["buscar_usuario.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_usuario.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir" => ["excluir_usuario.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
    ],
    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar" => ["alterar_cliente.php", "alterar_fornecedor.php"]
    ],
    3 => [
        "Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],
        "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir" => ["excluir_produto.php"]
    ],
    4 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Alterar" => ["alterar_cliente.php"]
    ]
];

// Carrega opções do menu com base no perfil
$opcoes_menu = $permissoes[$id_perfil] ?? [];

// Busca de funcionários
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Funcionário - Sistema de Biblioteca</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .dropdown {
            position: relative;
        }

        .dropdown>a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: block;
            font-weight: 500;
            cursor: pointer;
        }

        .dropdown>a:hover,
        .dropdown.open>a {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            list-style: none;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 1000;
        }

        .dropdown.open .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li a {
            color: #1e3a8a;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: block;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem;
        }

        .dropdown-menu li a:hover {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            transform: translateX(5px);
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Form Styles */
        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1e3a8a;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.6);
        }

        /* Table Styles */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(59, 130, 246, 0.1);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        /* Action Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            box-shadow: 0 2px 10px rgba(30, 58, 138, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.5);
            color: white;
            text-decoration: none;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.5);
            color: white;
            text-decoration: none;
        }

        /* No Results Message */
        .no-results {
            text-align: center;
            color: white;
            font-size: 1.2rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin: 2rem 0;
        }

        /* Back Button */
        .back-button {
            text-align: center;
            margin-top: 2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            h2 {
                font-size: 2rem;
            }

            .table-container {
                padding: 1rem;
            }

            th,
            td {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>


    <!-- Header -->
    <header
        style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <nav
            style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <h1
                style="color: white; margin: 0; font-size: 1.5rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                📚 Sistema de Biblioteca
            </h1>

            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div style="position: relative; display: inline-block;">
                    <button onclick="toggleDropdown()"
                        style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        📋 Menu ▼
                    </button>
                    <div id="dropdown"
                        style="display: none; position: absolute; right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); min-width: 200px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border-radius: 12px; z-index: 1000; border: 1px solid rgba(255, 255, 255, 0.2); margin-top: 0.5rem;">
                        <a href="cadastro_funcionario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Funcionario</a>
                        <a href="buscar_funcionario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Funcionario</a>
                        <a href="alterar_funcionario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Funcionario</a>
                        <a href="excluir_funcionario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Funcionario</a>
                        <a href="principal.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠
                            Painel Principal</a>
                    </div>
                </div>

                <!-- Logout -->
                <a href="logout.php"
                    style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">
                    🚪 Sair
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h2>📋 Lista de Funcionários</h2>

        <!-- Formulário de busca -->
        <form action="buscar_funcionario.php" method="POST">
            <label for="busca">Digite o ID ou Nome (opcional):</label>
            <input type="text" id="busca" name="busca" placeholder="Digite o ID ou nome do funcionário...">
            <button type="submit">Pesquisar</button>
        </form>

        <!-- Tabela de resultados -->
        <?php if (!empty($funcionarios)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $func): ?>
                            <tr>
                                <td><?= htmlspecialchars($func['id_funcionario']) ?></td>
                                <td><?= htmlspecialchars($func['nome_funcionario']) ?></td>
                                <td><?= htmlspecialchars($func['endereco']) ?></td>
                                <td><?= htmlspecialchars($func['telefone']) ?></td>
                                <td><?= htmlspecialchars($func['email']) ?></td>
                                <td>
                                    <a href="alterar_funcionario.php?id=<?= $func['id_funcionario'] ?>"
                                        class="btn btn-primary">✏️ Alterar</a>
                                    <a href="excluir_funcionario.php?id=<?= $func['id_funcionario'] ?>" class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                                        🗑️ Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>Nenhum funcionário encontrado.</p>
            </div>
        <?php endif; ?>

        <!-- Botão Voltar -->
        <div class="back-button">
            <a href="principal.php" class="btn btn-primary">🏠 Voltar ao Painel</a>
        </div>
    </div>

    <!-- Script para o dropdown -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Fecha o dropdown ao clicar fora
        window.onclick = function (event) {
            const dropdown = document.getElementById('dropdown');
            const button = event.target.closest('button');

            if (!button || !button.onclick || !button.onclick.toString().includes('toggleDropdown')) {
                dropdown.style.display = 'none';
            }
        };
    </script> 

</body>

</html>