<?php
session_start();
require_once 'conexao.php';

// Garante que o usuário esteja logado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Acesso negado.');window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem.');window.location.href='alterar_senha.php';</script>";
    } elseif (strlen($nova_senha) < 8) {
        echo "<script>alert('A senha deve ter pelo menos 8 caracteres.');</script>";
    } elseif ($nova_senha === "temp123") {
        echo "<script>alert('Escolha uma senha diferente de temporaria.');</script>";
    } else {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha e remove o status de temporaria
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = FALSE WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindparam(':senha', $senha_hash);
        $stmt->bindParam(':id', $id_usuario);

        if ($stmt->execute()) {
            session_destroy(); // Finaliza a sessão do usuário
            echo "<script>alert('Senha alterada com sucesso! Faça login novamente.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Erro ao alterar a senha!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
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
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        p {
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 16px;
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

        input[type="password"] {
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            outline: none;
        }

        input[type="password"]:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 10px;
        }

        input[type="checkbox"] {
            margin-right: 8px;
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
                font-size: 22px;
            }

            p {
                font-size: 14px;
            }

            input[type="password"], button {
                padding: 12px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Alterar Senha</h2>
        <p>Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>.<br>Digite sua nova senha abaixo:</p>

        <form action="alterar_senha.php" method="POST">
            <div>
                <label for="nova_senha">Nova Senha</label>
                <input type="password" name="nova_senha" id="nova_senha" required>
            </div>

            <div>
                <label for="confirmar_senha">Confirmar Senha</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" required>
            </div>

            <div class="checkbox-label">
                <input type="checkbox" onclick="mostrarSenha()">
                Mostrar Senha
            </div>

            <button type="submit">Salvar Nova Senha</button>
        </form>

        <div class="back-link">
            <a href="index.php">← Voltar para o login</a>
        </div>
    </div>

    <script>
        function mostrarSenha() {
            var senha1 = document.getElementById("nova_senha");
            var senha2 = document.getElementById("confirmar_senha");
            var tipo = senha1.type === "password" ? "text" : "password";
            senha1.type = tipo;
            senha2.type = tipo;
        }
    </script>
</body>
</html>