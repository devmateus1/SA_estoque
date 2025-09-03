<?php
session_start();
require_once 'conexao.php';

if(!isset($_SESSION['usuario'])){
    header('Location:login.php');
    exit();
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

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <!-- Removendo referências externas e aplicando CSS inline -->
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #334155 70%, #475569 100%); min-height: 100vh; color: #f8fafc; position: relative;">
    <!-- Adicionando padrão de fundo decorativo -->
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(147, 197, 253, 0.08) 0%, transparent 50%); pointer-events: none; z-index: 0;"></div>
    
    <header style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%); backdrop-filter: blur(20px); padding: 25px 35px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(59, 130, 246, 0.2); border-bottom: 2px solid rgba(59, 130, 246, 0.3); position: relative; z-index: 10;"> 
        <div class="saudacao" style="flex: 1;">
            <!-- Novo design do título com efeito neon -->
            <h2 style="margin: 0; background: linear-gradient(135deg, #3b82f6, #60a5fa, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 1.5rem; font-weight: 700; text-shadow: 0 0 20px rgba(59, 130, 246, 0.5); letter-spacing: -0.025em;"> 
                Bem vindo, <?php echo $_SESSION['usuario'];?> !<br>
                <span style="font-size: 1.1rem; font-weight: 500; opacity: 0.9;">Perfil: <?php echo $nome_perfil;?></span>
            </h2>    
        </div>

        <div class="logout">
            <form action="logout.php" method="POST" style="margin: 0;">
                <!-- Botão logout com novo design futurista -->
                <button type="submit" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; border: none; padding: 14px 28px; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2); position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-3px) scale(1.05)'; this.style.boxShadow='0 8px 30px rgba(220, 38, 38, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3)'; this.style.background='linear-gradient(135deg, #b91c1c, #dc2626)'" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 4px 20px rgba(220, 38, 38, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2)'; this.style.background='linear-gradient(135deg, #dc2626, #ef4444)'"> 
                    <span style="position: relative; z-index: 2;">🚪 Logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Nova navegação com design mais moderno e animações -->
    <nav style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(51, 65, 85, 0.8) 100%); backdrop-filter: blur(15px); padding: 0; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(59, 130, 246, 0.1); position: relative; z-index: 9;">
        <ul class="menu" style="list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; flex-wrap: wrap; gap: 2px;">
            <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
            <li class="dropdown" style="position: relative; display: inline-block;">
                <!-- Links do menu com efeito glassmorphism -->
                <a href="#" style="display: block; padding: 20px 30px; color: #e2e8f0; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); border-bottom: 3px solid transparent; position: relative; background: linear-gradient(135deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);" onmouseover="this.style.backgroundColor='rgba(59, 130, 246, 0.15)'; this.style.borderBottomColor='#3b82f6'; this.style.color='#ffffff'; this.style.textShadow='0 0 10px rgba(59, 130, 246, 0.8)'; this.nextElementSibling.style.display='block'; this.nextElementSibling.style.opacity='1'; this.nextElementSibling.style.transform='translateY(0)'" onmouseout="this.style.backgroundColor='rgba(59, 130, 246, 0.05)'; this.style.borderBottomColor='transparent'; this.style.color='#e2e8f0'; this.style.textShadow='none'"><?php echo $categoria; ?></a>
                
                <!-- Dropdown com animações suaves e design moderno -->
                <ul class="dropdown-menu" style="display: none; opacity: 0; transform: translateY(-10px); position: absolute; top: 100%; left: 50%; transform: translateX(-50%) translateY(-10px); background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%); min-width: 280px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 12px 0; z-index: 1000; backdrop-filter: blur(20px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseenter="this.style.display='block'; this.style.opacity='1'; this.style.transform='translateX(-50%) translateY(0)'" onmouseleave="this.style.opacity='0'; this.style.transform='translateX(-50%) translateY(-10px)'; setTimeout(() => { if(this.style.opacity === '0') this.style.display='none'; }, 300)">
                    <?php foreach($arquivos as $arquivo):?>
                        <li style="list-style: none;">
                            <!-- Items do dropdown com hover effects elaborados -->
                            <a href="<?php echo htmlspecialchars($arquivo); ?>" style="display: block; padding: 14px 24px; color: #cbd5e1; text-decoration: none; font-size: 1rem; font-weight: 500; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 3px solid transparent; position: relative; background: linear-gradient(90deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);" onmouseover="this.style.backgroundColor='rgba(59, 130, 246, 0.2)'; this.style.borderLeftColor='#60a5fa'; this.style.paddingLeft='32px'; this.style.color='#ffffff'; this.style.textShadow='0 0 8px rgba(96, 165, 250, 0.6)'" onmouseout="this.style.backgroundColor='rgba(59, 130, 246, 0.05)'; this.style.borderLeftColor='transparent'; this.style.paddingLeft='24px'; this.style.color='#cbd5e1'; this.style.textShadow='none'"> 
                                <span style="position: relative; z-index: 2;">📋 <?php echo ucfirst(str_replace("_", " ", basename($arquivo, ".php"))); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?> 
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- JavaScript aprimorado com animações mais suaves -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.dropdown-menu');
                let timeoutId;
                
                dropdown.addEventListener('mouseenter', () => {
                    clearTimeout(timeoutId);
                    menu.style.display = 'block';
                    setTimeout(() => {
                        menu.style.opacity = '1';
                        menu.style.transform = 'translateX(-50%) translateY(0)';
                    }, 10);
                });
                
                dropdown.addEventListener('mouseleave', () => {
                    menu.style.opacity = '0';
                    menu.style.transform = 'translateX(-50%) translateY(-10px)';
                    timeoutId = setTimeout(() => {
                        menu.style.display = 'none';
                    }, 300);
                });
            });

            // Efeito de partículas no fundo
            const createParticle = () => {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 2px;
                    height: 2px;
                    background: rgba(59, 130, 246, 0.6);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 1;
                    animation: float 6s linear infinite;
                `;
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = '100%';
                document.body.appendChild(particle);
                
                setTimeout(() => particle.remove(), 6000);
            };

            // Adicionar CSS da animação
            const style = document.createElement('style');
            style.textContent = `
                @keyframes float {
                    to {
                        transform: translateY(-100vh) rotate(360deg);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Criar partículas periodicamente
            setInterval(createParticle, 2000);
        });
    </script>
</body>
</html>
