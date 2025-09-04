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
                        <a href="cadastro_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(59, 130, 246, 0.1);">📚 Cadastrar Livro</a>
                        <a href="buscar_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋 Listar Livros</a>
                        <a href="alterar_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️ Alterar Livro</a>
                        <a href="excluir_fornecedor.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🗑️ Excluir Livro</a>
                        <a href="principal.php" style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠 Painel Principal</a>
                    </div>
                </div>
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
