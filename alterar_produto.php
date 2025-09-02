<?php
session_start();

// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=estoquebiblioteca', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// Verificar se o usuário está logado
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] < 1) {
    echo "Acesso negado. ";
    exit();
}

$mensagem = '';
$tipo_mensagem = '';
$livro = null;

// Buscar livro para edição
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$livro) {
        $mensagem = 'Livro não encontrado!';
        $tipo_mensagem = 'erro';
    }
}

// Processar formulário de atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $editora = trim($_POST['editora'] ?? '');
    $ano_publicacao = trim($_POST['ano_publicacao'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    
    // Validações
    if (empty($titulo) || empty($autor) || empty($isbn) || empty($editora) || empty($ano_publicacao) || empty($categoria)) {
        $mensagem = 'Todos os campos são obrigatórios!';
        $tipo_mensagem = 'erro';
    } elseif (!is_numeric($ano_publicacao) || $ano_publicacao < 1000 || $ano_publicacao > date('Y')) {
        $mensagem = 'Ano de publicação inválido!';
        $tipo_mensagem = 'erro';
    } elseif (strlen($isbn) < 10 || strlen($isbn) > 17) {
        $mensagem = 'ISBN deve ter entre 10 e 17 caracteres!';
        $tipo_mensagem = 'erro';
    } else {
        try {
            $sql = "UPDATE produtos SET titulo = :titulo, autor = :autor, isbn = :isbn, editora = :editora, ano_publicacao = :ano_publicacao, categoria = :categoria WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':editora', $editora);
            $stmt->bindParam(':ano_publicacao', $ano_publicacao);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $mensagem = 'Livro atualizado com sucesso!';
            $tipo_mensagem = 'sucesso';
            
            // Recarregar dados do livro
            $sql = "SELECT * FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $livro = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar livro: ' . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}

// Buscar todos os livros para seleção
$sql = "SELECT id_produto, titulo FROM produto ORDER BY titulo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$todos_livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Livro - Sistema de Biblioteca</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #333;">

    <!-- Header -->
    <header style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <nav style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <h1 style="color: white; margin: 0; font-size: 1.5rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                📚 Sistema de Biblioteca
            </h1>
            
            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div style="position: relative; display: inline-block;">
                    <button onclick="toggleDropdown()" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        📋 Menu ▼
                    </button>
                    <div id="dropdown" style="display: none; position: absolute; right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); min-width: 200px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border-radius: 12px; z-index: 1000; border: 1px solid rgba(255, 255, 255, 0.2); margin-top: 0.5rem;">
                        <a href="cadastro_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚 Cadastrar Livro</a>
                        <a href="listar_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋 Listar Livros</a>
                        <a href="alterar_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">✏️ Alterar Livro</a>
                        <a href="excluir_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🗑️ Excluir Livro</a>
                        <a href="principal.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠 Painel Principal</a>
                    </div>
                </div>
                
                <!-- Logout -->
                <a href="logout.php" style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">
                    🚪 Sair
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 100px); padding: 2rem;">
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 2.5rem; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); width: 100%; max-width: 600px; border: 1px solid rgba(255, 255, 255, 0.2);">
            
            <h2 style="text-align: center; color: #1e40af; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                ✏️ Alterar Livro
            </h2>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; <?php echo $tipo_mensagem === 'sucesso' ? 'background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if (!$livro && !isset($_GET['id'])): ?>
                <!-- Seleção de Livro -->
                <div style="margin-bottom: 2rem;">
                    <label style="font-weight: 600; color: #1e40af; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">📚 Selecione um livro para alterar:</label>
                    <select onchange="window.location.href='alterar_produto.php?id=' + this.value" 
                            style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;">
                        <option value="">Escolha um livro...</option>
                        <?php foreach ($todos_livros as $item): ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['titulo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <?php if ($livro): ?>
                <!-- Formulário de Edição -->
                <form method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <input type="hidden" name="id" value="<?php echo $livro['id']; ?>">
                    
                    <!-- Título -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="titulo" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📖 Título do Livro:</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($livro['titulo']); ?>" required
                               style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <!-- Autor -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="autor" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">✍️ Autor:</label>
                        <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($livro['autor']); ?>" required
                               style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <!-- ISBN -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="isbn" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">🔢 ISBN:</label>
                        <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($livro['isbn']); ?>" required
                               style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <!-- Editora -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="editora" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">🏢 Editora:</label>
                        <input type="text" id="editora" name="editora" value="<?php echo htmlspecialchars($livro['editora']); ?>" required
                               style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <!-- Ano de Publicação -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="ano_publicacao" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📅 Ano de Publicação:</label>
                        <input type="number" id="ano_publicacao" name="ano_publicacao" value="<?php echo htmlspecialchars($livro['ano_publicacao']); ?>" 
                               min="1000" max="<?php echo date('Y'); ?>" required
                               style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <!-- Categoria -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="categoria" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📂 Categoria:</label>
                        <select id="categoria" name="categoria" required
                                style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            <option value="">Selecione uma categoria</option>
                            <option value="Ficção" <?php echo $livro['categoria'] === 'Ficção' ? 'selected' : ''; ?>>Ficção</option>
                            <option value="Não-ficção" <?php echo $livro['categoria'] === 'Não-ficção' ? 'selected' : ''; ?>>Não-ficção</option>
                            <option value="Romance" <?php echo $livro['categoria'] === 'Romance' ? 'selected' : ''; ?>>Romance</option>
                            <option value="Mistério" <?php echo $livro['categoria'] === 'Mistério' ? 'selected' : ''; ?>>Mistério</option>
                            <option value="Fantasia" <?php echo $livro['categoria'] === 'Fantasia' ? 'selected' : ''; ?>>Fantasia</option>
                            <option value="Biografia" <?php echo $livro['categoria'] === 'Biografia' ? 'selected' : ''; ?>>Biografia</option>
                            <option value="História" <?php echo $livro['categoria'] === 'História' ? 'selected' : ''; ?>>História</option>
                            <option value="Ciência" <?php echo $livro['categoria'] === 'Ciência' ? 'selected' : ''; ?>>Ciência</option>
                            <option value="Tecnologia" <?php echo $livro['categoria'] === 'Tecnologia' ? 'selected' : ''; ?>>Tecnologia</option>
                            <option value="Educação" <?php echo $livro['categoria'] === 'Educação' ? 'selected' : ''; ?>>Educação</option>
                        </select>
                    </div>

                    <!-- Botões -->
                    <div style="display: flex; gap: 1rem; width: 100%; box-sizing: border-box;">
                        <button type="submit" 
                                style="flex: 1; background: linear-gradient(135deg, #059669, #10b981); color: white; border: none; padding: 1rem; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(16, 185, 129, 0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(16, 185, 129, 0.3)'">
                            ✏️ Atualizar Livro
                        </button>
                        <a href="listar_produto.php" 
                           style="flex: 1; background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; text-decoration: none; padding: 1rem; border-radius: 8px; font-size: 1.1rem; font-weight: 600; text-align: center; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(107, 114, 128, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(107, 114, 128, 0.3)'">
                            📋 Ver Lista
                        </a>
                    </div>

                </form>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Fechar dropdown ao clicar fora
        window.onclick = function(event) {
            if (!event.target.matches('button')) {
                const dropdown = document.getElementById('dropdown');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
            }
        }

        // Adicionar efeitos hover aos links do dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownLinks = document.querySelectorAll('#dropdown a');
            dropdownLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(59, 130, 246, 0.1)';
                    this.style.transform = 'translateX(4px)';
                });
                link.addEventListener('mouseleave', function() {
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
