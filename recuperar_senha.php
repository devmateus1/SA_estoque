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
    <title>Recuperar Senha</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #1e3a8a;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            color: #374151;
            font-weight: 500;
            text-align: left;
            margin-bottom: 5px;
            font-size: 14px;
        }

        input[type="email"] {
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
        }

        input[type="email"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        button {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        }

        button:active {
            transform: translateY(-1px);
        }

        .back-link {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .back-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
        }

        .back-link a:hover {
            background: rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }

            h2 {
                font-size: 24px;
            }

            input[type="email"], button {
                padding: 12px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Senha</h2>
        <form action="recuperar_senha.php" method="POST">
            <div>
                <label for="email">Digite seu E-mail cadastrado:</label>
                <input type="email" name="email" id="email" required placeholder="seu@email.com">
            </div>
            
            <button type="submit">Enviar Nova Senha</button>
        </form>
        
        <div class="back-link">
            <a href="login.php">← Voltar para o login</a>
        </div>
    </div>
</body>
</html>
