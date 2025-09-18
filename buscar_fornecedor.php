<?php
session_start();
require_once 'conexao.php';

// Verifica permissão (apenas Admin e Gerente podem buscar Editora — perfis 1 e 3)
if (!in_array($_SESSION['perfil'], [1, 3])) {
    echo "Acesso negado.";
    exit();
}

// Inicializa variáveis
$mensagem = '';
$tipo_mensagem = '';
$resultados = [];
$termo_busca = '';

// Processa a busca
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $termo_busca = trim($_POST['termo_busca'] ?? '');

    if (empty($termo_busca)) {
        $mensagem = 'Digite um termo para buscar.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            $sql = "SELECT id_Editora, nome_Editora, endereco, telefone, email, contato 
                    FROM Editora 
                    WHERE nome_Editora LIKE :termo 
                       OR telefone LIKE :termo 
                       OR email LIKE :termo 
                       OR contato LIKE :termo
                    ORDER BY nome_Editora ASC";
            $stmt = $pdo->prepare($sql);
            $termo = "%{$termo_busca}%";
            $stmt->bindParam(':termo', $termo);

            if ($stmt->execute()) {
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($resultados) === 0) {
                    $mensagem = 'Nenhum Editora encontrado.';
                    $tipo_mensagem = 'erro';
                }
            } else {
                $mensagem = 'Erro ao buscar Editoraes.';
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
    <title>Buscar Editora - Sistema de Biblioteca</title>
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
            align-items: flex-start;
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
            max-width: 800px;
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
            margin-bottom: 1.5rem;
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

        .btn-buscar {
            width: 100%;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            box-shadow: 0 4px 20px rgba(30, 64, 175, 0.3);
            margin-bottom: 2rem;
        }

        .btn-buscar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(30, 64, 175, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #1e40af;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(59, 130, 246, 0.05);
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

        @media (max-width: 768px) {
            th, td {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <nav>
            <h1>📚 Sistema de Biblioteca</h1>
            <div style="display: flex; align-items: center; gap: 2rem;">
                <!-- Menu Dropdown -->
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-btn">📋 Menu ▼</button>
                    <div id="dropdown" class="dropdown-content">
                        <a href="cadastro_Editora.php">📚 Cadastrar Editora</a>
                        <a href="buscar_Editora.php">📋 Listar Editora</a>
                        <a href="alterar_Editora.php">✏️ Alterar Editora</a>
                        <a href="excluir_Editora.php" style="background: rgba(239, 68, 68, 0.1);">🗑️ Excluir Editora</a>
                        <a href="principal.php">🏠 Painel Principal</a>
                    </div>
                </div>

                <!-- Logout -->
                <a href="logout.php" class="logout-btn">🚪 Sair</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h2>🔍 Buscar Editora</h2>

            <!-- Mensagem de feedback -->
            <?php if (!empty($mensagem)): ?>
                <div class="mensagem <?php echo $tipo_mensagem === 'sucesso' ? 'sucesso' : 'erro'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de Busca -->
            <form action="buscar_Editora.php" method="POST">
                <div class="form-group">
                    <label for="termo_busca">Digite o nome, telefone, email ou contato da editora:</label>
                    <input type="text" id="termo_busca" name="termo_busca" value="<?php echo htmlspecialchars($termo_busca); ?>" placeholder="Ex: Livraria Central, (11) 99999-9999, contato@livraria.com, João Silva" required>
                </div>
                <button type="submit" name="buscar" class="btn-buscar">🔍 Buscar Editora</button>
            </form>

            <!-- Resultados da Busca -->
            <?php if (!empty($resultados)): ?>
                <h3 style="color: #1e40af; margin: 1.5rem 0 1rem; font-weight: 600;">Resultados encontrados:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Contato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $Editora): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($Editora['id_Editora']); ?></td>
                                <td><?php echo htmlspecialchars($Editora['nome_Editora']); ?></td>
                                <td><?php echo htmlspecialchars($Editora['endereco']); ?></td>
                                <td><?php echo htmlspecialchars($Editora['telefone']); ?></td>
                                <td><?php echo htmlspecialchars($Editora['email']); ?></td>
                                <td><?php echo htmlspecialchars($Editora['contato']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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

    <!-- Script do Dropdown -->
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