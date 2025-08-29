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
    }else {
        // Login invalido
        echo "<script>alert('E-mail ou senha inválidos.');window.location.href='login.php';</script>";
    }
    }   
   

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Estoque - Login</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-header {
            background: #2c3e50;
            color: white;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: "📚";
            font-size: 48px;
            display: block;
            margin-bottom: 16px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 400;
        }

        .login-form {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #95a5a6;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            background: #667eea;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }

        .login-button:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
            color: #95a5a6;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e8ed;
            z-index: 1;
        }

        .divider span {
            background: white;
            padding: 0 16px;
            position: relative;
            z-index: 2;
        }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
            border-top: 1px solid #e1e8ed;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header,
            .login-form {
                padding: 30px 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Gerenciamento de Estoque</h1>
            <p>Acesse sua conta</p>
        </div>
        
        <form class="login-form">
            <div class="form-group">
                <label for="usuario">Usuário ou Email</label>
                <input 
                    type="text" 
                    id="usuario" 
                    name="usuario" 
                    placeholder="Digite seu usuário ou email"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    placeholder="Digite sua senha"
                    required
                >
            </div>
            
            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="lembrar">
                    <span>Lembrar de mim</span>
                </label>
                <a href="#" class="forgot-password">Esqueci minha senha</a>
            </div>
            
            <button type="submit" class="login-button">
                Entrar no Sistema
            </button>
            
            <div class="divider">
                <span>ou</span>
            </div>
            
            <div class="register-link">
                Não tem uma conta? <a href="cadastro_usuario.php">Cadastre-se aqui</a>
            </div>
        </form>
        
        <div class="footer">
            © 2024 Gerenciamento de Estoque. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>