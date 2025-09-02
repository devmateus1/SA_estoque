<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuario tem permissao 
// Supondo que o perfil 1 seja o adm
if ($_SESSION['perfil']!= 1) {
    echo "Acesso negado. ";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $nome = $_POST['nome'];
   $email = $_POST['email'];
   $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
   $id_perfil = $_POST['id_perfil'];
    
    $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':id_perfil', $id_perfil);

    if ( $stmt->execute()){
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    }else {
        echo "<script>alert('Erro ao cadastrar usuário.');</script>";
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
    <title>Cadastrar Usuário</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #ffffff;">

<!-- Adicionando CSS inline para navegação azul marinho -->
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

<!-- Adicionando container principal com CSS inline azul marinho -->
<div style="max-width: 500px; margin: 40px auto; padding: 40px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); border: 1px solid rgba(59, 130, 246, 0.2);">
    
    <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 30px; font-size: 28px; font-weight: 700; text-shadow: 0 2px 4px rgba(30, 58, 138, 0.1);">Cadastrar Usuário</h2>
    
    <form action="cadastro_usuario.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="nome" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Nome:</label>
            <input type="text" id="nome" name="nome" required onkeypress="mascara(this.nome)" 
                   style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937;"
                   onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                   onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="email" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Email:</label>
            <input type="email" name="email" id="email" required 
                   style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937;"
                   onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                   onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="senha" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Senha:</label>
            <input type="password" name="senha" id="senha" required 
                   style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937;"
                   onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                   onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="id_perfil" style="color: #1e3a8a; font-weight: 600; font-size: 14px;">Perfil:</label>
            <select name="id_perfil" id="id_perfil" 
                    style="padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; background: #ffffff; color: #1f2937; cursor: pointer;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.transform='translateY(-1px)';"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                <option value="1">Administrador</option>
                <option value="2">Secretária</option>
                <option value="3">Almoxarife</option>
                <option value="4">Cliente</option>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" 
                    style="flex: 1; padding: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #ffffff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(30, 58, 138, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)';">
                Salvar
            </button>
            
            <button type="reset" 
                    style="flex: 1; padding: 16px; background: linear-gradient(135deg, #6b7280, #9ca3af); color: #ffffff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(107, 114, 128, 0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(107, 114, 128, 0.3)';">
                Cancelar
            </button>
        </div>
        
    </form>

    <center style="margin-top: 30px;">
        <a href="principal.php" 
           style="display: inline-block; padding: 12px 24px; background: rgba(30, 58, 138, 0.1); color: #1e3a8a; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; border: 2px solid rgba(30, 58, 138, 0.2);"
           onmouseover="this.style.background='rgba(30, 58, 138, 0.2)'; this.style.transform='translateY(-1px)';"
           onmouseout="this.style.background='rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(0)';">
           Voltar
        </a>
    </center>
    
</div>
    
</body>
</html>
