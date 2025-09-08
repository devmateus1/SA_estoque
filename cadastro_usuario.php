<?php
session_start();
require_once 'conexao.php'; // Certifique-se de que este arquivo retorna $pdo

// Verifica se o usuário tem permissão (apenas perfil 1 - Admin)
if ($_SESSION['perfil'] != 1) {
    echo "Acesso negado.";
    exit();
}

// Inicializa mensagem
$mensagem = '';

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $id_perfil = $_POST['id_perfil'] ?? null;

    // Validação básica
    if (empty($nome) || empty($email) || empty($senha) || empty($id_perfil)) {
        $mensagem = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Email inválido.";
    } else {
        try {
            // Verifica se o email já existe
            $sql_check = "SELECT id_usuario FROM usuario WHERE email = :email";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $mensagem = "Este email já está cadastrado.";
            } else {
                // Insere o novo usuário
                $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', password_hash($senha, PASSWORD_DEFAULT));
                $stmt->bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $mensagem = "Usuário cadastrado com sucesso!";
                    // Opcional: redirecionar para evitar reenvio do formulário
                    // header('Location: cadastro_usuario.php?success=1');
                    // exit();
                } else {
                    $mensagem = "Erro ao cadastrar usuário.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - Sistema de Biblioteca</title>
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
            color: #ffffff;
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
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin: 0;
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
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(30, 58, 138, 0.1);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        label {
            color: #1e3a8a;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select {
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #ffffff;
            color: #1f2937;
        }

        input:focus,
        select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
            transform: translateY(-1px);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }

        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background: rgba(30, 58, 138, 0.1);
            color: #1e3a8a;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(30, 58, 138, 0.2);
            text-align: center;
            margin-top: 30px;
        }

        .back-link:hover {
            background: rgba(30, 58, 138, 0.2);
            transform: translateY(-1px);
        }

        .mensagem {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
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
                        <a href="cadastro_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📚
                            Cadastrar Livro</a>
                        <a href="buscar_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">📋
                            Listar Livros</a>
                        <a href="alterar_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px;">✏️
                            Alterar Livro</a>
                        <a href="excluir_produto.php"
                            style="color: #1e40af; padding: 12px 16px; text-decoration: none; display: block; transition: all 0.3s ease; border-radius: 8px; margin: 4px; background: rgba(239, 68, 68, 0.1);">🗑️
                            Excluir Livro</a>
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
            <h2>Cadastrar Usuário</h2>

            <!-- Mensagem de feedback -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <form action="cadastro_usuario.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome"
                        value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <div class="form-group">
                    <label for="id_perfil">Perfil:</label>
                    <select name="id_perfil" id="id_perfil" required>
                        <option value="1" <?php echo ($_POST['id_perfil'] ?? '') == '1' ? 'selected' : ''; ?>>
                            Administrador</option>
                        <option value="2" <?php echo ($_POST['id_perfil'] ?? '') == '2' ? 'selected' : ''; ?>>Funcionário
                        </option>
                        <option value="3" <?php echo ($_POST['id_perfil'] ?? '') == '3' ? 'selected' : ''; ?>>Gerente
                        </option>
                        <option value="4" <?php echo ($_POST['id_perfil'] ?? '') == '4' ? 'selected' : ''; ?>>Cliente
                        </option>
                    </select>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="reset" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>

            <a href="principal.php" class="back-link">Voltar</a>
        </div>
    </main>

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