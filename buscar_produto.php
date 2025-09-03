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

if ($_SESSION['perfil']!= 1) {
    echo "Acesso negado. ";
    exit();
}


// Buscar todos os livros
$sql = "SELECT * FROM produto ORDER BY titulo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Obtendo o nome do perfil do usuario logado 
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

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Livros - Sistema de Biblioteca</title>
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
                        <a href="listar_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">📋 Listar Livros</a>
                        <a href="alterar_produto.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️ Alterar Livro</a>
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
    <main style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 2.5rem; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.2);">
            
            <h2 style="text-align: center; color: #1e40af; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                📋 Lista de Livros
            </h2>

            <?php if (count($livros) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white;">
                                <th style="padding: 1rem; text-align: left; border-radius: 8px 0 0 0;">📖 Título</th>
                                <th style="padding: 1rem; text-align: left;">✍️ Autor</th>
                                <th style="padding: 1rem; text-align: left;">🔢 ISBN</th>
                                <th style="padding: 1rem; text-align: left;">🏢 Editora</th>
                                <th style="padding: 1rem; text-align: left;">📅 Ano</th>
                                <th style="padding: 1rem; text-align: left;">📂 Categoria</th>
                                <th style="padding: 1rem; text-align: center; border-radius: 0 8px 0 0;">⚙️ Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($livros as $index => $livro): ?>
                                <tr style="background: <?php echo $index % 2 == 0 ? 'rgba(59, 130, 246, 0.05)' : 'white'; ?>; transition: all 0.3s ease;" 
                                    onmouseover="this.style.background='rgba(59, 130, 246, 0.1)'" 
                                    onmouseout="this.style.background='<?php echo $index % 2 == 0 ? 'rgba(59, 130, 246, 0.05)' : 'white'; ?>'">
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['autor']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['isbn']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['editora']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['ano_publicacao']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1);"><?php echo htmlspecialchars($livro['categoria']); ?></td>
                                    <td style="padding: 1rem; border-bottom: 1px solid rgba(59, 130, 246, 0.1); text-align: center;">
                                        <a href="alterar_produto.php?id=<?php echo $livro['id']; ?>" 
                                           style="background: linear-gradient(135deg, #059669, #10b981); color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8rem; margin-right: 0.5rem; display: inline-block; transition: all 0.3s ease;">
                                            ✏️ Editar
                                        </a>
                                        <a href="excluir_produto.php?id=<?php echo $livro['id']; ?>" 
                                           style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8rem; display: inline-block; transition: all 0.3s ease;"
                                           onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                            🗑️ Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #6b7280;">
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">📚 Nenhum livro cadastrado ainda.</p>
                    <a href="cadastro_produto.php" style="background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; text-decoration: none; padding: 1rem 2rem; border-radius: 8px; font-weight: 600; display: inline-block; transition: all 0.3s ease;">
                        ➕ Cadastrar Primeiro Livro
                    </a>
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
