<?php
    session_start();
    require_once 'conexao.php';

    if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] !=2) {
        echo "<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
        exit();
    }

// Inicializa a variável para evitar erros 
$usuarios = [];

// Se o formulário for enviado, busca o usuário pelo id ou nome.

if ($_SERVER["REQUEST_METHOD"] ==  "POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    // Verifica se a busca é um número (id) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome like :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM usuario ORDER BY nome ASC";       
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar usuário</title>
    <!-- Removendo links externos e aplicando CSS inline -->
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); min-height: 100vh; color: #1f2937;">

<!-- Aplicando estilos inline na navegação -->
<nav style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
    <ul class="menu" style="list-style: none; margin: 0; padding: 0; display: flex; justify-content: center; gap: 30px; position: relative;">
        <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
        <li class="dropdown" style="position: relative;">
            <a href="#" style="color: white; text-decoration: none; padding: 12px 20px; display: block; font-weight: 600; font-size: 16px; border-radius: 8px; transition: all 0.3s ease; background: linear-gradient(45deg, rgba(59, 130, 246, 0.2), rgba(30, 64, 175, 0.2)); border: 1px solid rgba(59, 130, 246, 0.3);" 
               onmouseover="this.style.background='linear-gradient(45deg, rgba(59, 130, 246, 0.4), rgba(30, 64, 175, 0.4))'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.3)';" 
               onmouseout="this.style.background='linear-gradient(45deg, rgba(59, 130, 246, 0.2), rgba(30, 64, 175, 0.2))'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
               <?php echo $categoria; ?>
            </a>
            <ul class="dropdown-menu" style="position: absolute; top: 100%; left: 0; background: rgba(30, 58, 138, 0.98); backdrop-filter: blur(15px); list-style: none; padding: 10px 0; margin: 0; min-width: 200px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.4); border: 1px solid rgba(59, 130, 246, 0.3); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000;"
                onmouseenter="this.style.opacity='1'; this.style.visibility='visible'; this.style.transform='translateY(0)';"
                onmouseleave="this.style.opacity='0'; this.style.visibility='hidden'; this.style.transform='translateY(-10px)';">
                <?php foreach($arquivos as $arquivo):?>
                    <li>
                        <a href="<?=$arquivo ?>" style="color: #e5e7eb; text-decoration: none; padding: 12px 20px; display: block; transition: all 0.3s ease; font-size: 14px;" 
                           onmouseover="this.style.background='rgba(59, 130, 246, 0.3)'; this.style.color='white'; this.style.paddingLeft='25px';" 
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

<!-- Aplicando estilos inline no conteúdo principal -->
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <h2 style="text-align: center; color: white; font-size: 32px; font-weight: 700; margin-bottom: 40px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); background: linear-gradient(45deg, #60a5fa, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Lista de Usuários</h2>
    
    <!-- Estilizando o formulário de busca -->
    <form action="buscar_usuario.php" method="POST" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); margin-bottom: 30px; border: 1px solid rgba(59, 130, 246, 0.2);">
        <div style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
            <label for="busca" style="color: #1e40af; font-weight: 600; font-size: 16px;">Digite o ID ou Nome (opcional):</label>
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; justify-content: center;">
                <input type="text" id="busca" name="busca" style="padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 16px; width: 300px; transition: all 0.3s ease; background: white;" 
                       onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'; this.style.outline='none';" 
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                <button type="submit" style="background: linear-gradient(45deg, #1e40af, #3b82f6); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);" 
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(59, 130, 246, 0.4)';" 
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(59, 130, 246, 0.3)';">
                    Pesquisar
                </button>
            </div>
        </div>
    </form>

    <?php if(!empty($usuarios)):?>
    <!-- Estilizando a tabela -->
    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.2); border: 1px solid rgba(59, 130, 246, 0.2);">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background: linear-gradient(45deg, #1e40af, #3b82f6); color: white;">
                    <th style="padding: 16px; text-align: left; font-weight: 600; border-bottom: 2px solid rgba(59, 130, 246, 0.3);">ID</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; border-bottom: 2px solid rgba(59, 130, 246, 0.3);">Nome</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; border-bottom: 2px solid rgba(59, 130, 246, 0.3);">Email</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; border-bottom: 2px solid rgba(59, 130, 246, 0.3);">Perfil</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600; border-bottom: 2px solid rgba(59, 130, 246, 0.3);">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $index => $usuario): ?>
                    <tr style="<?= $index % 2 == 0 ? 'background: rgba(59, 130, 246, 0.05);' : 'background: white;' ?> transition: all 0.3s ease;" 
                        onmouseover="this.style.background='rgba(59, 130, 246, 0.1)'; this.style.transform='scale(1.01)';" 
                        onmouseout="this.style.background='<?= $index % 2 == 0 ? 'rgba(59, 130, 246, 0.05)' : 'white' ?>'; this.style.transform='scale(1)';">
                        <td style="padding: 16px; border-bottom: 1px solid rgba(59, 130, 246, 0.1); color: #1f2937; font-weight: 500;"><?=htmlspecialchars($usuario['id_usuario']) ?></td>
                        <td style="padding: 16px; border-bottom: 1px solid rgba(59, 130, 246, 0.1); color: #1f2937; font-weight: 500;"><?=htmlspecialchars($usuario['nome']) ?></td>
                        <td style="padding: 16px; border-bottom: 1px solid rgba(59, 130, 246, 0.1); color: #1f2937;"><?=htmlspecialchars($usuario['email']) ?></td>
                        <td style="padding: 16px; border-bottom: 1px solid rgba(59, 130, 246, 0.1); color: #1f2937; font-weight: 500;"><?=htmlspecialchars($usuario['id_perfil']) ?></td>
                        <td style="padding: 16px; border-bottom: 1px solid rgba(59, 130, 246, 0.1); text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="alterar_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" style="background: linear-gradient(45deg, #059669, #10b981); color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);" 
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4)';" 
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.3)';">
                                   Alterar
                                </a>
                                <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('Tem certeza que deseja excluir este usuario?')" style="background: linear-gradient(45deg, #dc2626, #ef4444); color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);" 
                                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(239, 68, 68, 0.4)';" 
                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(239, 68, 68, 0.3)';">
                                   Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>   
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 40px; border-radius: 16px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.2); border: 1px solid rgba(59, 130, 246, 0.2);">
            <p style="color: #6b7280; font-size: 18px; margin: 0;">Nenhum usuário encontrado.</p>
        </div>
    <?php endif; ?> 
    
    <!-- Estilizando o botão voltar -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="principal.php" style="background: linear-gradient(45deg, #1e40af, #3b82f6); color: white; text-decoration: none; padding: 14px 28px; border-radius: 12px; font-size: 16px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); display: inline-block;" 
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(59, 130, 246, 0.4)';" 
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(59, 130, 246, 0.3)';">
           Voltar
        </a>
    </div>
</div>

</body>
</html>
