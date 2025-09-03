<?php
session_start();
require_once 'conexao.php';


// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil']!= 1) {
    echo"<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
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

// Inicializa as variaveis 
$fornecedor = null;

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM fornecedor";
$stmt = $pdo->prepare($sql); 
$stmt->execute();
$fornecedores = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Se um id for passado via GET, excluir o usuário 
if (isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_fornecedor = $_GET['id'];
    
    // excluir o usuario do banco de dados 
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_fornecedor, PDO::PARAM_INT);

    if ($stmt->execute()){
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
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%); min-height: 100vh;">
    
    <!-- Applied inline CSS navigation with blur effect and modern styling -->
    <nav style="background: rgba(30, 58, 138, 0.9); backdrop-filter: blur(10px); padding: 1rem 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
            <ul style="list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; gap: 2rem;">
                <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
                <li style="position: relative;">
                    <a href="#" onclick="toggleDropdown(event, this)" style="color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; transition: all 0.3s ease; display: block; font-weight: 500; background: rgba(255, 255, 255, 0.1);" 
                       onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(-2px)'" 
                       onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(0)'"><?= $categoria ?></a>
                    <ul style="position: absolute; top: 100%; left: 0; background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); min-width: 200px; border-radius: 8px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2); display: none; z-index: 1000; margin-top: 0.5rem; list-style: none; padding: 0.5rem 0;">
                        <?php foreach($arquivos as $arquivo): ?>
                        <li>
                            <a href="<?= $arquivo ?>" style="color: white; text-decoration: none; padding: 0.75rem 1.5rem; display: block; transition: all 0.3s ease;" 
                               onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.paddingLeft='2rem'" 
                               onmouseout="this.style.background='transparent'; this.style.paddingLeft='1.5rem'"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <!-- Applied modern container styling with blur effect and shadows -->
    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); padding: 2rem; border: 1px solid rgba(255, 255, 255, 0.2);">
            
            <!-- Applied modern title styling with text shadow -->
            <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);">Excluir Fornecedor</h2>
            
            <?php if(!empty($fornecedores)):?>
                <!-- Applied modern table styling with hover effects -->
                <div style="overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">Nome</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">Email</th>
                                <th style="padding: 1rem; text-align: left; color: white; font-weight: 600; border: none;">Contatos</th>
                                <th style="padding: 1rem; text-align: center; color: white; font-weight: 600; border: none;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;" 
                                    onmouseover="this.style.background='#f8fafc'; this.style.transform='scale(1.01)'" 
                                    onmouseout="this.style.background='white'; this.style.transform='scale(1)'">
                                    <td style="padding: 1rem; border: none;"><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                                    <td style="padding: 1rem; border: none; font-weight: 500;"><?= htmlspecialchars($fornecedor['nome_fornecedor']) ?></td>
                                    <td style="padding: 1rem; border: none; color: #6b7280;"><?= htmlspecialchars($fornecedor['email']) ?></td>
                                    <td style="padding: 1rem; border: none; color: #6b7280;"><?= htmlspecialchars($fornecedor['contato']) ?></td>
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
                <p style="text-align: center; color: #6b7280; font-size: 1.1rem; margin: 2rem 0;">Nenhum Fornecedor encontrado.</p>
            <?php endif; ?>
            
            <!-- Applied modern button styling with gradient and hover effects -->
            <div style="text-align: center; margin-top: 2rem;">
                <a href="principal.php" 
                   style="background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; padding: 0.75rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.15)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)'">Voltar</a>
            </div>
        </div>
    </div>

    <!-- Added modern JavaScript for dropdown functionality -->
    <script>
        function toggleDropdown(event, element) {
            event.preventDefault();
            const dropdown = element.nextElementSibling;
            const isVisible = dropdown.style.display === 'block';
            
            // Fechar todos os dropdowns
            document.querySelectorAll('nav ul li ul').forEach(menu => {
                menu.style.display = 'none';
            });
            
            // Abrir o dropdown clicado se não estava visível
            if (!isVisible) {
                dropdown.style.display = 'block';
            }
        }

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            if (!event.target.closest('nav')) {
                document.querySelectorAll('nav ul li ul').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
