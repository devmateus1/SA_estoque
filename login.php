<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário já está logado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o usuário existe
    $sql ="SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido, define variáveis de sessão
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['id_perfil'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];

        // Veriifica se a senha é temporária
        if ($usuario['senha_temporaria']) {
            // Redireciona para a página de alteração de senha
            header('Location: alterar_senha.php');
            exit();
        } else {
            // Redireciona para a pagina principal
            header('Location: principal.php');
            exit();
        }
    } else {
        // Login invalido
        echo "<script>alert('E-mail ou senha inválidos.');window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
 
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: linear-gradient(45deg, #0f172a 0%, #1e293b 50%, #334155 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    
    <div style="background: #e5e7eb; backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1); width: 100%; max-width: 400px; margin: 10px; border: 1px solid rgba(255, 255, 255, 0.2);">
        
        <h2 style="text-align: center; color: #0f172a; margin-bottom: 32px; font-size: 32px; font-weight: 700; letter-spacing: -1px; background: linear-gradient(135deg, #0f172a 0%, #e5e7eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Login</h2>
        
        <form action="login.php" method="POST" style="background-color: #e5e7eb; display: flex; flex-direction: column; gap: 20px; width: 100%; margin-left: -20px;">
            
            <div style="display: flex; flex-direction: column; background:#e5e7eb" weight="600">
                <label for="email" style="color: #374151; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">E-mail</label>
                <input type="email" name="email" id="email" required 
                       style="padding: 10px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none; width: 100%; box-sizing: border-box; background: #f9fafb;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 4px rgba(59, 130, 246, 0.1)'; this.style.background='#ffffff'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.background='#f9fafb'">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="senha" style="color: #374151; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Senha</label>
                <input type="password" name="senha" id="senha" required 
                       style="padding: 10px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none; width: 100%; box-sizing: border-box; background: #f9fafb;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 4px rgba(59, 130, 246, 0.1)'; this.style.background='#ffffff'"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.background='#f9fafb'">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                <button type="submit" 
                        style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 18px 24px; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); width: 100%; box-sizing: border-box; text-transform: uppercase; letter-spacing: 0.5px;"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(30, 64, 175, 0.4)'; this.style.background='linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.background='linear-gradient(135deg, #1e40af 0%, #3b82f6 100%)'"
                        onmousedown="this.style.transform='translateY(-1px)'"
                        onmouseup="this.style.transform='translateY(-3px)'">
                    Entrar
                </button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            <a href="recuperar_senha.php" 
               style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s ease; padding: 8px 16px; border-radius: 8px;"
               onmouseover="this.style.color='#1d4ed8'; this.style.background='rgba(59, 130, 246, 0.1)'"
               onmouseout="this.style.color='#3b82f6'; this.style.background='transparent'">
                Esqueci minha senha
            </a>
        </div>
    </div>

</body>
</html>