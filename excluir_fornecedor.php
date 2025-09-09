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
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"], // Admin
        "Buscar" => ["buscar_usuario.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_usuario.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir" => ["excluir_usuario.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
    ],

    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"], // Funcionario
        "Alterar" => ["alterar_cliente.php", "alterar_fornecedor.php"]
    ],

    3 => [
        "Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],         // Gerente
        "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir" => ["excluir_produto.php"]
    ],

    4 => [
        "Cadastrar" => ["cadastro_cliente.php"],   // Cliente
        "Alterar" => ["alterar_cliente.php"]
    ]
];

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa as variaveis 
$fornecedor = null;

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM fornecedor";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Se um id for passado via GET, excluir o usuário 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_fornecedor = $_GET['id'];

    // excluir o usuario do banco de dados 
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_fornecedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Fornecedor excluído com sucesso!');window.location.href='excluir_fornecedor.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir ao excluir Fornecedor.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%); min-height: 100vh;">
   
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
                        <a href="cadastro_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Fornecedor</a>
                        <a href="buscar_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Fornecedor</a>
                        <a href="alterar_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Fornecedor</a>
                        <a href="excluir_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Fornecedor</a>
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

    <!-- Applied modern container styling with blur effect and shadows -->
    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <div
            style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.2);">

            <!-- Applied modern title styling with text shadow -->
            <h2
                style="text-align: center; color: #1e3a8a; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);">
                Excluir Fornecedor</h2>

            <?php if (!empty($fornecedores)): ?>
                <!-- Applied modern table styling with hover effects -->
                <div style="overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <table
                        style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">
                                    ID</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">
                                    Nome</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">
                                    Email</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">
                                    Contatos</th>
                                <th
                                    style="padding: 1rem; text-align: center; color: white; font-weight: 600; border: none;">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;"
                                    onmouseover="this.style.background='#f8fafc'; this.style.transform='scale(1.01)'"
                                    onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                    <td style="padding: 1rem; border: none;">
                                        <?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                                    <td style="padding: 1rem; border: none; font-weight: 500;">
                                        <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?></td>
                                    <td style="padding: 1rem; border: none; color: #6b7280;">
                                        <?= htmlspecialchars($fornecedor['email']) ?></td>
                                    <td style="padding: 1rem; border: none; color: #6b7280;">
                                        <?= htmlspecialchars($fornecedor['contato']) ?></td>
                                    <td style="padding: 1rem; text-align: center; border: none;">
                                        <a href="excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"
                                            onclick="return confirm('Tem certeza que deseja excluir este Fornecedor?')"
                                            style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(220, 38, 38, 0.3)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(220, 38, 38, 0.2)'">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #6b7280; font-size: 1.1rem; margin: 2rem 0;">Nenhum Fornecedor
                    encontrado.</p>
            <?php endif; ?>

        </div>
    </div>

    
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