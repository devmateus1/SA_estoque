<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// DEFINIÇÃO DAS PERMISSÕES POR PERFIL

$permissoes = [

    1 =>
        [
            "Cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
            "Buscar" => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
            "Alterar" => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
            "Excluir" => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
        ],

    2 =>
        [
            "Cadastrar" => ["cadastro_cliente.php"],
            "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
            "Alterar" => ["alterar_cliente.php", "alterar_fornecedor.php"]
        ],

    3 =>
        [
            "Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],
            "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
            "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],
            "Excluir" => ["excluir_produto.php"]
        ],

    4 =>
        [
            "Cadastrar" => ["cadastro_cliente.php"],
            "Buscar" => ["buscar_produto.php"],
            "Alterar" => ["alterar_cliente.php"]
        ],

];

// OBTENDO AS OPÇÕS DISPONIVEIS PARA O PERFIL LOGADO

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa as variaveis 
$fornecedor = null;

// Se o formulário for enviado, busca o usuário pelo id ou nome.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['busca_fornecedor'])) {
        $busca = trim($_POST['busca_fornecedor']);

        // Verifica se a busca é um número (id) ou um nome
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor like :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
        }
        $stmt->execute();
        $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o usuário não for encontrado, exibe um alerta 
        if (!$fornecedor) {
            echo "<script>alert('Fornecedor não encontrado.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Fornecedor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            min-height: 100vh;
            color: #333;
        }

        header {
            background: rgba(30, 58, 138, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 0.5rem;
        }

        .dropdown-content a {
            color: #1e40af;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 4px;
        }

        .dropdown-content a:hover {
            background: rgba(59, 130, 246, 0.1);
            transform: translateX(4px);
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        main {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 100px);
            padding: 2rem;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: 600;
            color: #1e40af;
            font-size: 0.9rem;
        }

        input {
            padding: 0.875rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: #1f2937;
        }

        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        .btn-buscar,
        .btn-alterar,
        .btn-cancelar {
            width: 100%;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-buscar {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);
        }

        .btn-buscar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(30, 64, 175, 0.4);
        }

        .btn-alterar {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            box-shadow: 0 4px 20px rgba(22, 163, 74, 0.3);
        }

        .btn-alterar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(22, 163, 74, 0.4);
        }

        .btn-cancelar {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
            box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);
        }

        .btn-cancelar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(107, 114, 128, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <nav>
            <h1>📚 Sistema de Biblioteca</h1>
            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-btn">📋 Menu ▼</button>
                    <div id="dropdown" class="dropdown-content">
                        <a href="cadastro_fornecedor.php">📚 Cadastrar Fornecedor</a>
                        <a href="buscar_fornecedor.php">📋 Listar Fornecedor</a>
                        <a href="alterar_fornecedor.php">✏️ Alterar Fornecedor</a>
                        <a href="excluir_fornecedor.php" style="background: rgba(239, 68, 68, 0.1);">🗑️ Excluir Fornecedor</a>
                        <a href="principal.php">🏠 Painel Principal</a>
                    </div>
                </div>

                <!-- Logout -->
                <a href="logout.php" class="logout-btn">🚪 Sair</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h2>✏️ Alterar Fornecedor</h2>

            <!-- Formulário para buscar fornecedor -->
            <form action="alterar_fornecedor.php" method="POST">
                <div class="form-group">
                    <label for="busca_fornecedor">Digite o ID ou Nome do fornecedor:</label>
                    <input type="text" id="busca_fornecedor" name="busca_fornecedor" required onkeyup="buscarSugestoes()">
                </div>
                <button type="submit" class="btn-buscar">🔍 Buscar Fornecedor</button>
            </form>

            <!-- Formulário de alteração (aparece após busca) -->
            <?php if ($fornecedor): ?>
                <form action="processa_alteracao_fornecedor.php" method="POST" style="margin-top: 2rem;">
                    <input type="hidden" name="id_fornecedor" value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">

                    <div class="form-group">
                        <label for="nome_fornecedor">Nome:</label>
                        <input type="text" id="nome_fornecedor" name="nome_fornecedor" value="<?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="endereco">Endereço:</label>
                        <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($fornecedor['endereco']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($fornecedor['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($fornecedor['telefone']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="contato">Contato:</label>
                        <input type="text" id="contato" name="contato" value="<?= htmlspecialchars($fornecedor['contato']) ?>" required>
                    </div>

                    <button type="submit" class="btn-alterar">✅ Alterar Fornecedor</button>
                    <button type="reset" class="btn-cancelar">❌ Cancelar</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <center style="margin-top: 30px;">
        <a href="principal.php"
            style="display: inline-block; padding: 16px 32px; background: rgba(255, 255, 255, 0.2); color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);"
            onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)';"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            ← Voltar ao Menu Principal
        </a>
    </center>

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