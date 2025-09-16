<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão
if ($_SESSION['perfil'] != 1) {
    echo "Acesso negado.";
    exit();
}

$id_perfil = $_SESSION['perfil'];

// Busca o nome do perfil
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// Permissões por perfil
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

$opcoes_menu = $permissoes[$id_perfil] ?? [];

// Processar o cadastro do fornecedor
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_fornecedor = trim($_POST['nome_fornecedor']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $contato = trim($_POST['contato']);

    if (!$email) {
        $mensagem = 'Email inválido.';
        $tipo_mensagem = 'erro';
    } else {
        $sql = "INSERT INTO fornecedor (nome_fornecedor, endereco, telefone, email, contato) 
                VALUES (:nome_fornecedor, :endereco, :telefone, :email, :contato)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contato', $contato);

        if ($stmt->execute()) {
            $mensagem = 'Fornecedor cadastrado com sucesso!';
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = 'Erro ao cadastrar fornecedor.';
            $tipo_mensagem = 'erro';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Editoria</title>
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
                        <a href="cadastro_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Editora</a>
                        <a href="buscar_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Editora</a>
                        <a href="alterar_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Editora</a>
                        <a href="excluir_fornecedor.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Editora</a>
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
    <main
        style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 100px); padding: 2rem;">
        <div
            style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 2.5rem; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); width: 100%; max-width: 600px; border: 1px solid rgba(255, 255, 255, 0.2);">

            <h2
                style="text-align: center; color: #1e3a8a; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                Cadastrar Editora
            </h2>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div
                    style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; <?php echo $tipo_mensagem === 'sucesso' ? 'background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="cadastro_fornecedor.php" method="POST"
                style="display: flex; flex-direction: column; gap: 1.5rem;">

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="nome_fornecedor" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">Nome da Editora:</label>
                    <input type="text" id="nome_fornecedor" name="nome_fornecedor" required
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="endereco" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="telefone" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" required
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="email" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">Email:</label>
                    <input type="email" id="email" name="email" required
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="contato" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">Contato:</label>
                    <input type="text" id="contato" name="contato" required
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <!-- Botões -->
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit"
                        style="flex: 1; padding: 1rem 2rem; background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(30, 64, 175, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(30, 64, 175, 0.3)'">
                        Salvar Editora
                    </button>

                    <button type="reset"
                        style="flex: 1; padding: 1rem 2rem; background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(107, 114, 128, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(107, 114, 128, 0.3)'">
                        Limpar Campos
                    </button>
                </div>
            </form>


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