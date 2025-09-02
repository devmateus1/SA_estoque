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

// Busca todos os usuários cadastrados em ordem alfabética
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Se um id for passado via GET, excluir o usuário 
if (isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_usuario = $_GET['id'];
    
    // excluir o usuario do banco de dados 
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()){
        echo "<script>alert('Usuário excluído com sucesso!');window.location.href='excluir_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir usuário.');</script>";
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
    <title>Excluir usuário</title>
    <!-- Removendo links externos e aplicando CSS inline -->
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #1f2937;">

<nav style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <ul class="menu" style="list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; flex-wrap: wrap;">
        <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
        <li class="dropdown" style="position: relative; margin: 0;">
            <a href="#" style="display: block; padding: 15px 20px; color: white; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border-bottom: 3px solid transparent;" 
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'; this.style.borderBottomColor='#60a5fa';" 
               onmouseout="this.style.backgroundColor='transparent'; this.style.borderBottomColor='transparent';">
                <?php echo $categoria; ?>
            </a>
            <ul class="dropdown-menu" style="position: absolute; top: 100%; left: 0; background: rgba(30, 58, 138, 0.98); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); list-style: none; padding: 8px 0; margin: 0; min-width: 200px; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000;">
                <?php foreach($arquivos as $arquivo):?>
                    <li style="margin: 0;">
                        <a href="<?=$arquivo ?>" style="display: block; padding: 12px 20px; color: #e5e7eb; text-decoration: none; transition: all 0.3s ease; border-left: 3px solid transparent;" 
                           onmouseover="this.style.backgroundColor='rgba(96,165,250,0.2)'; this.style.borderLeftColor='#60a5fa'; this.style.color='white';" 
                           onmouseout="this.style.backgroundColor='transparent'; this.style.borderLeftColor='transparent'; this.style.color='#e5e7eb';">
                            <?= ucfirst(str_replace("_", " ",basename ($arquivo, ".php")))?>
                        </a>
                    </li>
                <?php endforeach; ?> 
            </ul>
        </li>
        <?php endforeach; ?>
        <li style="margin-left: auto;">
            <a href="logout.php" style="display: block; padding: 15px 20px; color: #fca5a5; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border-radius: 6px; margin: 8px;" 
               onmouseover="this.style.backgroundColor='rgba(239,68,68,0.2)'; this.style.color='#ef4444';" 
               onmouseout="this.style.backgroundColor='transparent'; this.style.color='#fca5a5';">
                Logout
            </a>
        </li>
    </ul>
</nav>

<script>
document.querySelectorAll('.dropdown').forEach(dropdown => {
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
</script>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); padding: 40px; margin-bottom: 30px;">
        <h2 style="text-align: center; color: #1e3a8a; font-size: 2.5rem; font-weight: 700; margin: 0 0 30px 0; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Excluir Usuário</h2>
        
        <?php if(!empty($usuarios)):?>
            <div style="overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #1e3a8a, #1e40af);">
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; border: none;">ID</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; border: none;">Nome</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; border: none;">Email</th>
                            <th style="padding: 16px; text-align: left; color: white; font-weight: 600; border: none;">Perfil</th>
                            <th style="padding: 16px; text-align: center; color: white; font-weight: 600; border: none;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: all 0.3s ease;" 
                                onmouseover="this.style.backgroundColor='#f8fafc';" 
                                onmouseout="this.style.backgroundColor='white';">
                                <td style="padding: 16px; color: #374151; border: none;"><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                                <td style="padding: 16px; color: #374151; font-weight: 500; border: none;"><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td style="padding: 16px; color: #6b7280; border: none;"><?= htmlspecialchars($usuario['email']) ?></td>
                                <td style="padding: 16px; color: #374151; border: none;"><?= htmlspecialchars($usuario['id_perfil']) ?></td>
                                <td style="padding: 16px; text-align: center; border: none;">
                                    <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>" 
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                       style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; display: inline-block; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 16px rgba(220, 38, 38, 0.4)';"
                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(220, 38, 38, 0.3)';">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>     
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: rgba(239, 246, 255, 0.8); border-radius: 12px; border: 2px dashed #93c5fd;">
                <p style="color: #1e40af; font-size: 1.2rem; margin: 0;">Nenhum usuário encontrado.</p>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="principal.php" 
               style="background: linear-gradient(135deg, #1e3a8a, #1e40af); color: white; padding: 12px 32px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block; box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 24px rgba(30, 58, 138, 0.4)';"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(30, 58, 138, 0.3)';">
                Voltar
            </a>
        </div>
    </div>
</div>

</body>
</html>
