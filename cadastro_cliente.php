<?php 
session_start();
require_once 'conexao.php';

// Verifica permissão (apenas Admin ou perfis com permissão para cadastrar cliente)
// Conforme array $permissoes que você definiu, perfis 2, 3 e 4 podem cadastrar cliente
if (!in_array($_SESSION['perfil'], [1, 2, 3, 4])) {
    echo "Acesso negado.";
    exit();
}

// Inicializa mensagens
$mensagem = '';
$tipo_mensagem = '';

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cliente = trim($_POST['nome_cliente'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validação simples
    if (empty($nome_cliente) || empty($endereco) || empty($telefone) || empty($email)) {
        $mensagem = 'Todos os campos são obrigatórios.';
        $tipo_mensagem = 'erro';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Email inválido.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            $sql = "INSERT INTO cliente (nome_cliente, endereco, telefone, email) VALUES (:nome_cliente, :endereco, :telefone, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome_cliente', $nome_cliente);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                $mensagem = 'Cliente cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
                // Limpa os campos após sucesso
                $_POST = [];
            } else {
                $mensagem = 'Erro ao cadastrar cliente.';
                $tipo_mensagem = 'erro';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro: ' . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente - Sistema de Biblioteca</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            min-height: 100vh;
            color: #333;
        }

        header {
            background: rgba(30, 58, 138, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            z-index: 1000;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 0.5rem;
        }

        .dropdown-content a {
            color: #1e40af;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 4px;
        }

        .dropdown-content a:hover {
            background: rgba(59, 130, 246, 0.1);
            transform: translateX(4px);
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
            padding: 2rem;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mensagem {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .sucesso {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .erro {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-weight: 600;
            color: #1e40af;
            font-size: 0.9rem;
        }

        input {
            padding: 0.875rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: #1f2937;
        }

        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        input::placeholder {
            color: #9ca3af;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            flex: 1;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(30, 64, 175, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
            box-shadow: 0 4px 20px rgba(107, 114, 128, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(107, 114, 128, 0.4);
        }

        .back-link {
            display: inline-block;
            background: rgba(30, 64, 175, 0.1);
            color: #1e40af;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(30, 64, 175, 0.2);
            margin-top: 2rem;
            text-align: center;
            width: 100%;
        }

        .back-link:hover {
            background: rgba(30, 64, 175, 0.2);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>


    <!-- Header -->
    <header
        style="background: rgba(30, 58, 138, 0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <nav
            style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <h1
                style="color: white; margin: 0; font-size: 1.5rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                📚 Sistema de Biblioteca
            </h1>

            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div style="position: relative; display: inline-block;">
                    <button onclick="toggleDropdown()"
                        style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        📋 Menu ▼
                    </button>
                    <div id="dropdown"
                        style="display: none; position: absolute; right: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); min-width: 200px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border-radius: 12px; z-index: 1000; border: 1px solid rgba(255, 255, 255, 0.2); margin-top: 0.5rem;">
                        <a href="cadastro_cliente.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Cliente</a>
                        <a href="buscar_cliente.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Cliente</a>
                        <a href="alterar_cliente.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Cliente</a>
                        <a href="excluir_cliente.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Cliente</a>
                        <a href="principal.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">🏠
                            Painel Principal</a>
                    </div>
                </div>

                <!-- Logout -->
                <a href="logout.php"
                    style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">
                    🚪 Sair
                </a>
            </div>
        </nav>
    </header>
    <!-- Main Content -->
    <main>
        <div class="container">
            <h2>👤 Cadastrar Cliente</h2>

            <!-- Mensagem de feedback -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?php echo $tipo_mensagem === 'sucesso' ? 'sucesso' : 'erro'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="cadastro_cliente.php" method="POST">
                <div class="form-group">
                    <label for="nome_cliente">👤 Nome do cliente:</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo isset($_POST['nome_cliente']) ? htmlspecialchars($_POST['nome_cliente']) : ''; ?>" required placeholder="Digite o nome completo">
                </div>

                <div class="form-group">
                    <label for="endereco">📍 Endereço:</label>
                    <input type="text" id="endereco" name="endereco" value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>" required placeholder="Ex: Rua das Flores, 123">
                </div>

                <div class="form-group">
                    <label for="telefone">📞 Telefone:</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required maxlength="15" placeholder="(11) 99999-9999" onkeyup="mascaraTelefone(this)">
                </div>

                <div class="form-group">
                    <label for="email">📧 Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required placeholder="cliente@exemplo.com">
                </div>

                <!-- Botões -->
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Cadastrar Cliente</button>
                    <button type="reset" class="btn btn-secondary">🔄 Limpar Campos</button>
                </div>
            </form>

        </div>
    </main>

    
    <center style="margin-top: 30px;">
            <a href="principal.php"
                style="display: inline-block; padding: 16px 32px; background: rgba(255, 255, 255, 0.2); color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);"
                onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.2)';"
                onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                ← Voltar ao Menu Principal
            </a>
        </center>

    <!-- Script para o dropdown -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Fecha o dropdown ao clicar fora
        window.onclick = function (event) {
            const dropdown = document.getElementById('dropdown');
            const button = event.target.closest('button');

            if (!button || !button.onclick || !button.onclick.toString().includes('toggleDropdown')) {
                dropdown.style.display = 'none';
            }
        };
    </script>

</body>
</html>