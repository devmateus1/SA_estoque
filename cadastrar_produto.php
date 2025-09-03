<?php
session_start();
require_once 'conexao.php';
require_once 'funcao_dropdown.php';

// Verifica se o usuário tem permissão para acessar a página
// Supondo que o perfil 1 seja o administrador
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado. Você não tem permissão para acessar esta página.'); window.location.href='principal.php';</script>";
    exit();
}

    
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
}
$opcoes_menu = $permissoes[$id_perfil];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $editora = $_POST['editora'];
    $ano_publicacao = $_POST['ano_publicacao'];
    $categoria = $_POST['categoria'];
    $data_cadastro = $_POST['data_cadastro'];

    $sql = "INSERT INTO produto (titulo, autor, editora, ano_publicacao, categoria, data_cadastro) VALUES (:titulo, :autor, :editora, :ano_publicacao, :categoria, :data_cadastro)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':editora', $editora);
    $stmt->bindParam(':ano_publicacao', $ano_publicacao);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':data_cadastro', $data_cadastro);

    if ($stmt->execute()) {
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar produto. Tente novamente.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <style>
        /* Applied modern CSS styling based on previous alterar_usuario.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            min-height: 100vh;
            color: #1f2937;
        }

        nav {
            background: rgba(30, 58, 138, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu {
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin: 0;
        }

        .dropdown {
            position: relative;
        }

        .dropdown > a {
            display: block;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 5px;
        }

        .dropdown > a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            list-style: none;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li a {
            display: block;
            padding: 12px 20px;
            color: #1f2937;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 5px;
        }

        .dropdown-menu li a:hover {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            transform: translateX(5px);
        }

        h2 {
            text-align: center;
            color: white;
            margin: 40px 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        input[type="text"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
            transform: translateY(-2px);
        }

        button {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 5px;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
        }

        button[type="reset"] {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        button[type="reset"]:hover {
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .back-link a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
            }
            
            form {
                margin: 20px;
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 2rem;
                margin: 20px 0;
            }
        }
    </style>
</head>

<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h2>Cadastrar Produto</h2>
    
    <form action="cadastro_produto.php" method="POST">
        <label for="titulo">Título do livro:</label>
        <input type="text" id="titulo" name="titulo" required>
        
        <label for="autor">Autor:</label>
        <input type="text" id="autor" name="autor" required>
        
        <label for="editora">Editora:</label>   
        <input type="text" id="editora" name="editora" required>
        
         Fixed input type from "data" to "date" 
        <label for="ano_publicacao">Ano da Publicação:</label>
        <input type="date" id="ano_publicacao" name="ano_publicacao" required>

        <label for="categoria">Categoria:</label>
        <input type="text" id="categoria" name="categoria" required>

         Fixed input type from "data" to "date" 
        <label for="data_cadastro">Data Cadastro:</label>
        <input type="date" id="data_cadastro" name="data_cadastro" required>
        
        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
    
    <div class="back-link">
        <a href="principal.php">Voltar</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.style.opacity = '1';
                        menu.style.visibility = 'visible';
                        menu.style.transform = 'translateY(0)';
                    }
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    const menu = this.querySelector('.dropdown-menu');
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
