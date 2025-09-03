<?php
session_start();
require_once 'conexao.php';
// verifica se o usuario tem permissao 
if($_SESSION['perfil']!= 1) {
    echo "Acesso negado. ";
    exit();
}

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
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $contato = $_POST['contato'];
    $sql = "INSERT INTO fornecedor (nome_fornecedor, endereco, telefone, email, contato) VALUES (:nome_fornecedor, :endereco, :telefone, :email, :contato)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contato', $contato);

    if($stmt->execute()) {
        echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar Fornecedor.');</script>";
    }
};
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de fornecedor</title>
</head>
<body style="margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%); min-height: 100vh; color: #1f2937;">
    <!-- Updated navigation styling to match product registration exactly -->
    <nav style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); position: sticky; top: 0; z-index: 1000;">
        <ul style="list-style: none; display: flex; justify-content: center; align-items: center; padding: 0; margin: 0; flex-wrap: wrap;">
            <?php foreach($opcoes_menu as $categoria=>$arquivos): ?>
            <li style="position: relative;">
                <a href="#" style="display: block; padding: 15px 25px; color: white; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 5px;" 
                   onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)'">
                   <?php echo htmlspecialchars($categoria); ?>
                </a>
                <ul style="position: absolute; top: 100%; left: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); list-style: none; min-width: 200px; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; margin: 0; padding: 0; border: 1px solid rgba(255, 255, 255, 0.2);"
                    onmouseenter="this.style.opacity='1'; this.style.visibility='visible'; this.style.transform='translateY(0)'"
                    onmouseleave="this.style.opacity='0'; this.style.visibility='hidden'; this.style.transform='translateY(-10px)'">
                    <?php foreach($arquivos as $arquivo): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($arquivo); ?>" 
                           style="display: block; padding: 12px 20px; color: #1f2937; text-decoration: none; transition: all 0.3s ease; border-radius: 8px; margin: 5px;"
                           onmouseover="this.style.background='linear-gradient(135deg, #1e3a8a, #3b82f6)'; this.style.color='white'; this.style.transform='translateX(5px)'"
                           onmouseout="this.style.background='transparent'; this.style.color='#1f2937'; this.style.transform='translateX(0)'">
                           <?php echo ucfirst(str_replace("_"," ",basename($arquivo,".php"))); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Updated title styling to match product registration -->
    <h2 style="text-align: center; color: white; margin: 40px 0; font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Cadastro de Fornecedor</h2>
    
    <!-- Updated form container styling to match product registration exactly -->
    <div style="max-width: 600px; margin: 0 auto; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);">
        <form method="POST" action="cadastro_fornecedor.php" style="display: flex; flex-direction: column;">
            <div style="margin-bottom: 20px;">
                <label for="nome_fornecedor" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 1rem;">Nome Fornecedor:</label>
                <input type="text" id="nome_fornecedor" name="nome_fornecedor" required 
                       style="width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1e3a8a'; this.style.boxShadow='0 0 0 3px rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(-2px)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="endereco" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 1rem;">Endereço:</label>
                <input type="text" id="endereco" name="endereco" required 
                       style="width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1e3a8a'; this.style.boxShadow='0 0 0 3px rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(-2px)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="telefone" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 1rem;">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required 
                       style="width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1e3a8a'; this.style.boxShadow='0 0 0 3px rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(-2px)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 1rem;">Email:</label>
                <input type="email" id="email" name="email" required 
                       style="width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1e3a8a'; this.style.boxShadow='0 0 0 3px rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(-2px)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="contato" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1f2937; font-size: 1rem;">Contato:</label>
                <input type="text" id="contato" name="contato" required 
                       style="width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); box-sizing: border-box;"
                       onfocus="this.style.borderColor='#1e3a8a'; this.style.boxShadow='0 0 0 3px rgba(30, 58, 138, 0.1)'; this.style.transform='translateY(-2px)'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.transform='translateY(0)'">
            </div>

            <!-- Updated button styling to match product registration exactly -->
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 15px 30px; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(30, 58, 138, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(30, 58, 138, 0.3)'">
                        Salvar
                </button>
                <button type="reset" 
                        style="background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; padding: 15px 30px; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(107, 114, 128, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(107, 114, 128, 0.3)'">
                        Cancelar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Updated back link styling to match product registration -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="principal.php" 
           style="color: white; text-decoration: none; font-weight: 600; padding: 12px 24px; background: rgba(255, 255, 255, 0.1); border-radius: 25px; transition: all 0.3s ease; display: inline-block;"
           onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='translateY(0)'">
           Voltar
        </a>
    </div>

    <script>
        // Validação de telefone
        function validarTelefone() {
            const telefone = document.getElementById('telefone');
            let valor = telefone.value.replace(/\D/g, '');
            
            if (valor.length <= 11) {
                valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
                valor = valor.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
                telefone.value = valor;
            }
        }

        // Validação de nome do fornecedor
        function validarNomeFornecedor() {
            const nome = document.getElementById('nome_fornecedor');
            nome.value = nome.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        }

        // Aplicar validações aos campos
        document.getElementById('telefone').addEventListener('input', validarTelefone);
        document.getElementById('nome_fornecedor').addEventListener('input', validarNomeFornecedor);

        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('li[style*="position: relative"]');
            
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    const menu = this.querySelector('ul');
                    if (menu) {
                        menu.style.opacity = '1';
                        menu.style.visibility = 'visible';
                        menu.style.transform = 'translateY(0)';
                    }
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    const menu = this.querySelector('ul');
                    if (menu) {
                        menu.style.opacity = '0';
                        menu.style.visibility = 'hidden';
                        menu.style.transform = 'translateY(-10px)';
                    }
                });
            });
        });
    </script>
</body>
</html>
