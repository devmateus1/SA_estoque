<?php 
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1) {
    echo "Acesso negado. ";
    exit();
}
    
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

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome_funcionario = $_POST['nome_funcionario'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    try {
        $sql = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email ) VALUES (:nome_funcionario, :endereco, :telefone, :email)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_funcionario', $nome_funcionario);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);

        if ( $stmt->execute()){
            $mensagem = 'Funcionário cadastrado com sucesso!';
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = 'Erro ao cadastrar funcionário. Tente novamente.';
            $tipo_mensagem = 'erro';
        }
    } catch (PDOException $e) {
        $mensagem = 'Erro ao cadastrar funcionário: ' . $e->getMessage();
        $tipo_mensagem = 'erro';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário - Sistema de Biblioteca</title>
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
                        <a href="cadastro_funcionario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">👤 Cadastrar Funcionário</a>
                        <a href="buscar_funcionario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋 Buscar Funcionário</a>
                        <a href="alterar_funcionario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️ Alterar Funcionário</a>
                        <a href="excluir_funcionario.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🗑️ Excluir Funcionário</a>
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
            
            <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 2rem; font-size: 2rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                👤 Cadastrar Funcionário
            </h2>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)): ?>
                <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-weight: 500; <?php echo $tipo_mensagem === 'sucesso' ? 'background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);' : 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3);'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="cadastro_funcionario.php" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="nome_funcionario" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">👤 Nome do funcionário:</label>
                    <input type="text" id="nome_funcionario" name="nome_funcionario" required 
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="endereco" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📍 Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required 
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="telefone" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📞 Telefone:</label>
                    <input type="text" id="telefone" name="telefone" required maxlength="15"
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                           onkeyup="mascaraTelefone(this)"
                           placeholder="(11) 99999-9999">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="email" style="font-weight: 600; color: #1e40af; font-size: 0.9rem;">📧 Email:</label>
                    <input type="email" id="email" name="email" required 
                           style="padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: white; color: #1f2937;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                           placeholder="funcionario@exemplo.com">
                </div>

                <!-- Botões -->
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" 
                            style="flex: 1; padding: 1rem 2rem; background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(30, 64, 175, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(30, 64, 175, 0.3)'">
                        💾 Cadastrar Funcionário
                    </button>
                    
                    <button type="reset" 
                            style="flex: 1; padding: 1rem 2rem; background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 30px rgba(107, 114, 128, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(107, 114, 128, 0.3)'">
                        🔄 Limpar Campos
                    </button>
                </div>
            </form>

            <!-- Botão Voltar -->
            <div style="text-align: center; margin-top: 2rem;">
                <a href="principal.php" 
                   style="background: rgba(30, 64, 175, 0.1); color: #1e40af; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-block; transition: all 0.3s ease; border: 2px solid rgba(30, 64, 175, 0.2);"
                   onmouseover="this.style.background='rgba(30, 64, 175, 0.2)'; this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='rgba(30, 64, 175, 0.1)'; this.style.transform='translateY(0)'">
                    🏠 Voltar ao Painel
                </a>
            </div>

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

        // Máscara para telefone
        function mascaraTelefone(campo) {
            let valor = campo.value.replace(/\D/g, '');
            
            if (valor.length <= 10) {
                valor = valor.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            } else {
                valor = valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            }
            
            campo.value = valor;
        }
    </script>

</body>
</html>