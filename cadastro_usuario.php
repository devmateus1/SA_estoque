<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuario tem permissao 
// Supondo que o perfil 1 seja o adm


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $nome = $_POST['nome'];
   $email = $_POST['email'];
   $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    
    $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    

    if ( $stmt->execute()){
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    }else {
        echo "<script>alert('Erro ao cadastrar usuário.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Biblioteca - Cadastro de Usuário</title>
    <script src="scripts.js"></script>
    <script src="validacoes.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
    </style>
</head>
<body>
    
    <!-- Aprimorado o menu de navegação com design moderno -->
   
    <!-- Container principal com design moderno e responsivo -->
    <div style="max-width: 600px; margin: 0 auto; padding: 0 1rem;">
        <!-- Cabeçalho com ícone e título elegante -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="background: white; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M12 11h4"></path>
                    <path d="M12 16h4"></path>
                    <path d="M8 11h.01"></path>
                    <path d="M8 16h.01"></path>
                </svg>
            </div>
            <h2 style="color: white; font-size: 2rem; font-weight: 700; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); margin-bottom: 0.5rem;">Cadastrar Usuário</h2>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 1.1rem;">Sistema de Gerenciamento da Biblioteca</p>
        </div>

        <!-- Formulário com design moderno e elegante -->
        <form action="cadastro_usuario.php" method="POST" style="background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1); backdrop-filter: blur(10px);">
            <div style="margin-bottom: 1.5rem;">
                <label for="nome" style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 600; font-size: 0.95rem;">Nome Completo</label>
                <input type="text" id="nome" name="nome" required onkeypress="mascara(this.nome)" 
                       style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #f7fafc;"
                       onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'; this.style.background='white'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f7fafc'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 600; font-size: 0.95rem;">Email</label>
                <input type="email" name="email" id="email" required 
                       style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #f7fafc;"
                       onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'; this.style.background='white'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f7fafc'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="senha" style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 600; font-size: 0.95rem;">Senha</label>
                <input type="password" name="senha" id="senha" required 
                       style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #f7fafc;"
                       onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'; this.style.background='white'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f7fafc'">
            </div>

            <div style="margin-bottom: 2rem;">
                <label for="id_perfil" style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 600; font-size: 0.95rem;">Perfil de Acesso</label>
                <select name="id_perfil" id="id_perfil" 
                        style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: #f7fafc; cursor: pointer;"
                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'; this.style.background='white'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.background='#f7fafc'">
                    <option value="1">👑 Administrador</option>
                    <option value="2">📋 Secretária</option>
                    <option value="3">📦 Almoxarife</option>
                    <option value="4">👤 Cliente</option>
                </select>
            </div>

            <!-- Botões com design moderno e responsivo -->
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button type="submit" 
                        style="flex: 1; min-width: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)'"
                        onmousedown="this.style.transform='translateY(0)'"
                        onmouseup="this.style.transform='translateY(-2px)'">
                    ✅ Salvar Usuário
                </button>
                
                <button type="reset" 
                        style="flex: 1; min-width: 120px; background: white; color: #718096; border: 2px solid #e2e8f0; padding: 1rem 2rem; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"
                        onmouseover="this.style.borderColor='#cbd5e0'; this.style.color='#4a5568'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(0, 0, 0, 0.1)'"
                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#718096'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                        onmousedown="this.style.transform='translateY(0)'"
                        onmouseup="this.style.transform='translateY(-2px)'">
                    🔄 Limpar
                </button>
            </div>
        </form>

        <!-- Link de voltar com design elegante -->
        <div style="text-align: center; margin-top: 2rem;">
            <a href="index.php" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.2); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 50px; font-weight: 600; transition: all 0.3s ease; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3);"
               onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)'"
               onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Voltar ao Menu Principal
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('nav li');
            
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('ul');
                if (menu) {
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
                }
            });
        });
    </script>
</body>
</html>
