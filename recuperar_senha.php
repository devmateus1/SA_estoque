<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php'; // Arquivo com funções que geram a senha e silulam o envio

// Verifica se o usuário existe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verifica se o usuário existe
    $sql ="SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Gera uma nova senha temporária
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);
        
        // Atualiza a senha do usuário no banco de dados
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Envia a nova senha para o e-mail do usuário
        simularEnvioEmail($email, $senha_temporaria);
        echo "<script>alert('Uma nova senha temporaria foi gerada e enviada (simulação). Verifique o arquivo emails_simulados.txt');window.location.href='login.php';</script>";

    } else {
        echo "<script>alert('E-mail não encontrado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a4d3a 0%, #2d5a3d 100%); margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
    
    <div style="background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 32px rgba(26, 77, 58, 0.3); width: 100%; max-width: 400px; margin: 20px;">
        
        <h2 style="color: #1a4d3a; text-align: center; margin-bottom: 30px; font-size: 28px; font-weight: 600; text-shadow: 0 2px 4px rgba(26, 77, 58, 0.1);">Recuperar Senha</h2>
        
        <form action="recuperar_senha.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label for="email" style="color: #2d5a3d; font-weight: 500; font-size: 14px; margin-bottom: 5px;">Digite seu E-mail cadastrado:</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    required 
                    style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: all 0.3s ease; outline: none; background-color: #f9f9f9;"
                    onfocus="this.style.borderColor='#3a6b4a'; this.style.backgroundColor='#ffffff'; this.style.boxShadow='0 0 0 3px rgba(58, 107, 74, 0.1)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.backgroundColor='#f9f9f9'; this.style.boxShadow='none'"
                >
            </div>
            
            <button 
                type="submit" 
                style="background: linear-gradient(135deg, #2d5a3d 0%, #3a6b4a 100%); color: white; padding: 14px 24px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 10px;"
                onmouseover="this.style.background='linear-gradient(135deg, #1a4d3a 0%, #2d5a3d 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(26, 77, 58, 0.4)'"
                onmouseout="this.style.background='linear-gradient(135deg, #2d5a3d 0%, #3a6b4a 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                Enviar nova senha
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 25px; color: #666;">
            <a 
                href="index.php" 
                style="color: #3a6b4a; text-decoration: none; font-weight: 500; transition: all 0.3s ease; padding: 8px 12px; border-radius: 6px;"
                onmouseover="this.style.color='#1a4d3a'; this.style.backgroundColor='rgba(58, 107, 74, 0.1)'"
                onmouseout="this.style.color='#3a6b4a'; this.style.backgroundColor='transparent'"
            >
                ← Voltar para o login
            </a>
        </p>
    </div>
    
</body>
</html>
