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
if ($_SESSION['perfil']!= 1) {
    echo "Acesso negado. ";
    exit();
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        // Aqui você conectaria com o banco de dados para inserir o livro
        // Por enquanto, simularemos o sucesso
        $mensagem = 'Livro cadastrado com sucesso!';
        $tipo_mensagem = 'sucesso';
        
        // Limpar campos após sucesso
        $titulo = $autor = $isbn = $editora = $ano_publicacao = $categoria = '';
    }
//Obtendo o nome do perfil do usuario logado 
    $id_perfil = $_SESSION['perfil'];
    $sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
    $stmtPerfil = $pdo->prepare($sqlPerfil);
    $stmtPerfil->bindParam(':id_perfil', $id_perfil);
    $stmtPerfil->execute();
    $perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil = $perfil['nome_perfil'];
    
    
    $permissoes = [
        1=> ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
            "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
            "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
            "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],
    
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
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Livro - Sistema de Biblioteca</title>
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
                        <?php if (in_array($perfil_usuario, ['Admin', 'Gerente'])): ?>
                            <a href="cadastrar_usuario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">👤 Cadastrar Usuário</a>
                            <a href="cadastrar_livro.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">📚 Cadastrar Livro</a>
                        <?php endif; ?>
                        <?php if ($perfil_usuario === 'Admin'): ?>
                            <a href="buscar_usuario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🔍 Buscar Usuário</a>
                            <a href="excluir_usuario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🗑️ Excluir Usuário</a>
                        <?php endif; ?>
                        <a href="buscar_livro.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📖 Buscar Livro</a>
                        <a href="painel_principal.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠 Painel Principal</a>
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
                📚 Cadastrar Novo Livro
            </h2>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; <?php echo $tipo_mensagem === 'sucesso' ? 'background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Título -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="titulo" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📖 Título do Livro:</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo ?? ''); ?>" required
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                </div>

                <!-- Autor -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="autor" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">✍️ Autor:</label>
                    <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($autor ?? ''); ?>" required
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                </div>

                <!-- ISBN -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="isbn" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">🔢 ISBN:</label>
                    <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn ?? ''); ?>" required
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                </div>

                <!-- Editora -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="editora" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">🏢 Editora:</label>
                    <input type="text" id="editora" name="editora" value="<?php echo htmlspecialchars($editora ?? ''); ?>" required
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; width: 100%; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                </div>

                <!-- Ano de Publicação -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="ano_publicacao" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📅 Ano de Publicação:</label>
                    <input type="number" id="ano_publicacao" name="ano_publicacao" value="<?php echo htmlspecialchars($ano_publicacao ?? ''); ?>" 
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
                        <option value="Ficção" <?php echo ($categoria ?? '') === 'Ficção' ? 'selected' : ''; ?>>Ficção</option>
                        <option value="Não-ficção" <?php echo ($categoria ?? '') === 'Não-ficção' ? 'selected' : ''; ?>>Não-ficção</option>
                        <option value="Romance" <?php echo ($categoria ?? '') === 'Romance' ? 'selected' : ''; ?>>Romance</option>
                        <option value="Mistério" <?php echo ($categoria ?? '') === 'Mistério' ? 'selected' : ''; ?>>Mistério</option>
                        <option value="Fantasia" <?php echo ($categoria ?? '') === 'Fantasia' ? 'selected' : ''; ?>>Fantasia</option>
                        <option value="Biografia" <?php echo ($categoria ?? '') === 'Biografia' ? 'selected' : ''; ?>>Biografia</option>
                        <option value="História" <?php echo ($categoria ?? '') === 'História' ? 'selected' : ''; ?>>História</option>
                        <option value="Ciência" <?php echo ($categoria ?? '') === 'Ciência' ? 'selected' : ''; ?>>Ciência</option>
                        <option value="Tecnologia" <?php echo ($categoria ?? '') === 'Tecnologia' ? 'selected' : ''; ?>>Tecnologia</option>
                        <option value="Educação" <?php echo ($categoria ?? '') === 'Educação' ? 'selected' : ''; ?>>Educação</option>
                    </select>
                </div>

                <!-- Botão Submit -->
                <div style="width: 100%; box-sizing: border-box;">
                    <button type="submit" 
                            style="width: 100%; background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; border: none; padding: 1rem; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); box-sizing: border-box;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(59, 130, 246, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(59, 130, 246, 0.3)'">
                        📚 Cadastrar Livro
                    </button>
                </div>

            </form>
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
