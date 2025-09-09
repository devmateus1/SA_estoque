<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa as variaveis 
$usuario = null;

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Se um id for passado via GET, excluir o usuário 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // excluir o usuario do banco de dados 
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário excluído com sucesso!');window.location.href='excluir_usuario.php';</script>";
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

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir usuário</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #ffffff;">

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
                        <a href="cadastro_usuario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Usuario</a>
                        <a href="buscar_usuario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Usuario</a>
                        <a href="alterar_usuario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Usuario</a>
                        <a href="excluir_usuario.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Usuario</a>
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


    <!-- Container principal -->
    <div
        style="max-width: 1200px; margin: 40px auto; padding: 40px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); border: 1px solid rgba(59, 130, 246, 0.2);">

        <h2
            style="text-align: center; color: #1e3a8a; margin-bottom: 30px; font-size: 28px; font-weight: 700; text-shadow: 0 2px 4px rgba(30, 58, 138, 0.1);">
            Excluir Usuário</h2>

        <?php if (!empty($usuarios)): ?>
            <div
                style="overflow-x: auto; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid rgba(30, 58, 138, 0.1);">
                <table style="width: 100%; border-collapse: collapse; margin: 0;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                            <th
                                style="padding: 20px 16px; text-align: left; color: white; font-weight: 600; border: none; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                ID</th>
                            <th
                                style="padding: 20px 16px; text-align: left; color: white; font-weight: 600; border: none; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Nome</th>
                            <th
                                style="padding: 20px 16px; text-align: left; color: white; font-weight: 600; border: none; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Email</th>
                            <th
                                style="padding: 20px 16px; text-align: left; color: white; font-weight: 600; border: none; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Perfil</th>
                            <th
                                style="padding: 20px 16px; text-align: center; color: white; font-weight: 600; border: none; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr style="border-bottom: 1px solid rgba(30, 58, 138, 0.1); transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='rgba(30, 58, 138, 0.05)'; this.style.transform='scale(1.01)';"
                                onmouseout="this.style.backgroundColor='white'; this.style.transform='scale(1)';">
                                <td style="padding: 18px 16px; color: #1f2937; border: none; font-weight: 500;">
                                    <?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                <td style="padding: 18px 16px; color: #1f2937; font-weight: 600; border: none;">
                                    <?= htmlspecialchars($usuario['nome']) ?></td>
                                <td style="padding: 18px 16px; color: #6b7280; border: none;">
                                    <?= htmlspecialchars($usuario['email']) ?></td>
                                <td style="padding: 18px 16px; color: #1f2937; border: none; font-weight: 500;">
                                    <?= htmlspecialchars($usuario['id_perfil']) ?></td>
                                <td style="padding: 18px 16px; text-align: center; border: none;">
                                    <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>"
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                        style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3); font-size: 0.9rem;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(220, 38, 38, 0.4)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(220, 38, 38, 0.3)';">
                                        🗑️ Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div
                style="text-align: center; padding: 40px; background: rgba(239, 246, 255, 0.5); border-radius: 16px; border: 2px dashed rgba(30, 58, 138, 0.3); backdrop-filter: blur(5px);">
                <div style="color: #1e40af; font-size: 3rem; margin-bottom: 16px;">📋</div>
                <p style="color: #1e40af; font-size: 1.2rem; margin: 0; font-weight: 500;">Nenhum usuário encontrado.</p>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="principal.php"
                style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3); font-size: 1rem;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(30, 58, 138, 0.4)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">
                🏠 Voltar
            </a>
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