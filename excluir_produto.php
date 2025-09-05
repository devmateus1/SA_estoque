<?php
session_start();
require_once 'conexao.php';
// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=estoquebiblioteca', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// Verificar se o usuário está logado

if ($_SESSION['perfil'] != 1) {
    echo "Acesso negado. ";
    exit();
}

$mensagem = '';
$tipo_mensagem = '';
$livro = null;

// Processar exclusão
if (isset($_GET['id']) && isset($_GET['confirmar'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM produto WHERE id_produto = :id_produto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_produto', $id_produto);
        $stmt->execute();

        $mensagem = 'Livro excluído com sucesso!';
        $tipo_mensagem = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao excluir livro: ' . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}

// Buscar livro para confirmação
if (isset($_GET['id_produto']) && !isset($_GET['confirmar'])) {
    $id = $_GET['id_produto'];
    $sql = "SELECT * FROM produto WHERE id_produto = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$livro) {
        $mensagem = 'Livro não encontrado!';
        $tipo_mensagem = 'erro';
    }
}

// Buscar todos os livros para seleção
$sql = "SELECT id_produto, titulo, autor FROM produto ORDER BY titulo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$todos_livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Livro - Sistema de Biblioteca</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #333;">

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
                        <a href="cadastro_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Livro</a>
                        <a href="buscar_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Livros</a>
                        <a href="alterar_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Livro</a>
                        <a href="excluir_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Livro</a>
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
                style="text-align: center; color: #dc2626; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                🗑️ Excluir Livro
            </h2>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div
                    style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; <?php echo $tipo_mensagem === 'sucesso' ? 'background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
                <?php if ($tipo_mensagem === 'sucesso'): ?>
                    <div style="text-align: center;">
                        <a href="listar_produto.php"
                            style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; text-decoration: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 600; display: inline-block; transition: all 0.3s ease;">
                            📋 Ver Lista de Livros
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$livro && !isset($_GET['id']) && empty($mensagem)): ?>
                <!-- Seleção de Livro -->
                <div style="margin-bottom: 2rem;">
                    <label
                        style="font-weight: 600; color: #dc2626; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">📚
                        Selecione um livro para excluir:</label>
                    <select onchange="window.location.href='excluir_produto.php?id=' + this.value"
                        style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;">
                        <option value="">Escolha um livro...</option>
                        <?php foreach ($todos_livros as $item): ?>
                            <option value="<?php echo $item['id']; ?>">
                                <?php echo htmlspecialchars($item['titulo']) . ' - ' . htmlspecialchars($item['autor']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if ($livro && !isset($_GET['confirmar'])): ?>
                <!-- Confirmação de Exclusão -->
                <div
                    style="background: rgba(239, 68, 68, 0.1); border: 2px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
                    <h3 style="color: #dc2626; margin-top: 0; text-align: center; font-size: 1.3rem;">⚠️ Confirmar Exclusão
                    </h3>
                    <p style="text-align: center; color: #374151; margin-bottom: 1.5rem;">Tem certeza que deseja excluir
                        este livro? Esta ação não pode ser desfeita.</p>

                    <div style="background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1rem; align-items: center;">
                            <strong style="color: #1e40af;">📖 Título:</strong>
                            <span><?php echo htmlspecialchars($livro['titulo']); ?></span>

                            <strong style="color: #1e40af;">✍️ Autor:</strong>
                            <span><?php echo htmlspecialchars($livro['autor']); ?></span>

                            <strong style="color: #1e40af;">🔢 ISBN:</strong>
                            <span><?php echo htmlspecialchars($livro['isbn']); ?></span>

                            <strong style="color: #1e40af;">🏢 Editora:</strong>
                            <span><?php echo htmlspecialchars($livro['editora']); ?></span>

                            <strong style="color: #1e40af;">📅 Ano:</strong>
                            <span><?php echo htmlspecialchars($livro['ano_publicacao']); ?></span>

                            <strong style="color: #1e40af;">📂 Categoria:</strong>
                            <span><?php echo htmlspecialchars($livro['categoria']); ?></span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="excluir_produto.php?id=<?php echo $livro['id']; ?>&confirmar=1"
                            style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; text-decoration: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(220, 38, 38, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(220, 38, 38, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(220, 38, 38, 0.3)'">
                            🗑️ Sim, Excluir
                        </a>
                        <a href="listar_produto.php"
                            style="background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; text-decoration: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(107, 114, 128, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(107, 114, 128, 0.3)'">
                            ❌ Cancelar
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Fechar dropdown ao clicar fora
        window.onclick = function (event) {
            if (!event.target.matches('button')) {
                const dropdown = document.getElementById('dropdown');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
            }
        }

        // Adicionar efeitos hover aos links do dropdown
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownLinks = document.querySelectorAll('#dropdown a');
            dropdownLinks.forEach(link => {
                link.addEventListener('mouseenter', function () {
                    this.style.background = 'rgba(59, 130, 246, 0.1)';
                    this.style.transform = 'translateX(4px)';
                });
                link.addEventListener('mouseleave', function () {
                    if (!this.style.background.includes('rgba(59, 130, 246, 0.1)')) {
                        this.style.background = 'transparent';
                    }
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>

</body>

</html>