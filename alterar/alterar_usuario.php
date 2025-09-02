<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil']!= 1) {
    echo"<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa as variaveis 
$usuario = null;

// Se o formulário for enviado, busca o usuário pelo id ou nome.
if ($_SERVER["REQUEST_METHOD"] ==  "POST"){

   if (!empty($_POST['busca_usuario'])){
    $busca = trim($_POST['busca_usuario']);

    // Verifica se a busca é um número (id) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome like :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se o usuário não for encontrado, exibe um alerta 
    if(!$usuario) {
        echo "<script>alert('Usuário não encontrado.');</script>";
    }
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

$opcoes_menu = $permissoes[$id_perfil]; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>
    <script src="scripts.js"></script>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #ffffff;">

<nav style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); padding: 15px 0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
    <ul class="menu" style="list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
        <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
        <li class="dropdown" style="position: relative;">
            <a href="#" style="color: #ffffff; text-decoration: none; padding: 12px 20px; display: block; font-weight: 600; font-size: 16px; border-radius: 8px; transition: all 0.3s ease; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.3);" 
               onmouseover="this.style.background='rgba(59, 130, 246, 0.4)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(59, 130, 246, 0.3)';" 
               onmouseout="this.style.background='rgba(59, 130, 246, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
               <?php echo $categoria; ?>
            </a>
            <ul class="dropdown-menu" style="position: absolute; top: 100%; left: 0; background: rgba(30, 58, 138, 0.98); backdrop-filter: blur(15px); list-style: none; padding: 10px 0; margin: 0; min-width: 220px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4); border: 1px solid rgba(59, 130, 246, 0.3); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000;"
                onmouseenter="this.style.opacity='1'; this.style.visibility='visible'; this.style.transform='translateY(0)';"
                onmouseleave="this.style.opacity='0'; this.style.visibility='hidden'; this.style.transform='translateY(-10px)';">
                <?php foreach($arquivos as $arquivo):?>
                <li>
                    <a href="<?=$arquivo ?>" style="color: #e5e7eb; text-decoration: none; padding: 12px 20px; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 0 8px;"
                       onmouseover="this.style.background='rgba(59, 130, 246, 0.3)'; this.style.color='#ffffff'; this.style.paddingLeft='24px';"
                       onmouseout="this.style.background='transparent'; this.style.color='#e5e7eb'; this.style.paddingLeft='20px';">
                       <?= ucfirst(str_replace("_", " ",basename ($arquivo, ".php")))?>
                    </a>
                </li>
                <?php endforeach; ?> 
            </ul>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<div style="max-width: 600px; margin: 40px auto; padding: 0 20px;">
    
    <h2 style="text-align: center; color: #ffffff; margin-bottom: 40px; font-size: 32px; font-weight: 700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">Alterar Usuário</h2>
    
    <form action="alterar_usuario.php" method="POST" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); padding: 30px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); border: 1px solid rgba(59, 130, 246, 0.2); margin-bottom: 30px;">
        
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
            <label for="busca_usuario" style="color: #1e3a8a; font-weight: 600; font-size: 16px;">Digite o ID ou Nome do usuário:</label>
            <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()" 
                   style="padding: 16px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; box-sizing: border-box;"
                   onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                   onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
        </div>

        <div id="sugestoes" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 20px; overflow: hidden;"></div>
        
        <button type="submit" 
                style="width: 100%; padding: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #ffffff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(30, 58, 138, 0.4)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">
            Buscar Usuário
        </button>
    </form>

    <?php if($usuario): ?>
        <form action="processa_alteracao_usuario.php" method="POST" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); padding: 30px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); border: 1px solid rgba(59, 130, 246, 0.2); margin-bottom: 30px;">
            <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">

            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="nome" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required 
                           style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="email" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Email:</label>
                    <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required 
                           style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="id_perfil" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Perfil:</label>
                    <select name="id_perfil" id="id_perfil" 
                            style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; cursor: pointer; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                        <option value="1" <?=($usuario['id_perfil'] == 1 ? 'selected': '' )?>>Administrador</option>
                        <option value="2" <?=($usuario['id_perfil'] == 2 ? 'selected': '' )?>>Secretária</option>
                        <option value="3" <?=($usuario['id_perfil'] == 3 ? 'selected': '' )?>>Almoxarife</option>
                        <option value="4" <?=($usuario['id_perfil'] == 4 ? 'selected': '' )?>>Cliente</option>
                    </select>
                </div>

                <?php if ($_SESSION['perfil'] === 1): ?>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="nova_senha" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Nova Senha (opcional):</label>
                    <input type="password" id="nova_senha" name="nova_senha" 
                           style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                           onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                </div>
                <?php endif; ?>

                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" 
                            style="flex: 1; padding: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #ffffff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(30, 58, 138, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">
                        Alterar
                    </button>
                    
                    <button type="reset" 
                            style="flex: 1; padding: 16px; background: linear-gradient(135deg, #6b7280, #9ca3af); color: #ffffff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(107, 114, 128, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        Cancelar
                    </button>
                </div>
                
            </div>
        </form>     
    <?php endif; ?>
    
    <center style="margin-top: 30px;">
        <a href="principal.php" 
           style="display: inline-block; padding: 16px 32px; background: rgba(255, 255, 255, 0.2); color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);"
           onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)';"
           onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
           ← Voltar ao Menu Principal
        </a>
    </center>
    
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        dropdown.addEventListener('mouseenter', () => {
            menu.style.opacity = '1';
            menu.style.visibility = 'visible';
            menu.style.transform = 'translateY(0)';
        });
        dropdown.addEventListener('mouseleave', () => {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.transform = 'translateY(-10px)';
        });
    });
});
</script>
</body>
</html>
