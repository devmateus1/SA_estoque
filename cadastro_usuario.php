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
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            min-height: 100vh;
            color: #333;
        }
    </style>
</head>
<body>
    
    <!-- <CHANGE> Adicionado menu dropdown moderno no topo -->
    <nav style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); padding: 0; position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem;">
            <!-- Logo/Brand -->
            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem 0;">
                <div style="background: linear-gradient(135deg, #2563eb, #1e40af); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <span style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">BiblioSystem</span>
            </div>

            <!-- Menu Principal -->
            <ul style="display: flex; list-style: none; gap: 0; margin: 0; padding: 0;">
                <!-- Usuários -->
                <li style="position: relative;">
                    <a href="#" style="display: flex; align-items: center; gap: 0.5rem; padding: 1.25rem 1.5rem; text-decoration: none; color: #4a5568; font-weight: 600; transition: all 0.3s ease; border-radius: 8px;"
                       onmouseover="this.style.color='#2563eb'; this.style.background='rgba(37, 99, 235, 0.1)'"
                       onmouseout="this.style.color='#4a5568'; this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Usuários
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </a>
                    <ul style="position: absolute; top: 100%; left: 0; background: white; min-width: 220px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); border-radius: 12px; padding: 0.5rem 0; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; border: 1px solid rgba(0, 0, 0, 0.1);">
                        <li><a href="cadastro_usuario.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Cadastrar Usuário</a></li>
                        <li><a href="listar_usuarios.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Listar Usuários</a></li>
                    </ul>
                </li>

                <!-- Livros -->
                <li style="position: relative;">
                    <a href="#" style="display: flex; align-items: center; gap: 0.5rem; padding: 1.25rem 1.5rem; text-decoration: none; color: #4a5568; font-weight: 600; transition: all 0.3s ease; border-radius: 8px;"
                       onmouseover="this.style.color='#2563eb'; this.style.background='rgba(37, 99, 235, 0.1)'"
                       onmouseout="this.style.color='#4a5568'; this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Livros
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </a>
                    <ul style="position: absolute; top: 100%; left: 0; background: white; min-width: 220px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); border-radius: 12px; padding: 0.5rem 0; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; border: 1px solid rgba(0, 0, 0, 0.1);">
                        <li><a href="cadastro_livro.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10,9 9,9 8,9"></polyline>
                            </svg>
                            Cadastrar Livro</a></li>
                        <li><a href="estoque_livros.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            Estoque de Livros</a></li>
                    </ul>
                </li>

                <!-- Empréstimos -->
                <li style="position: relative;">
                    <a href="#" style="display: flex; align-items: center; gap: 0.5rem; padding: 1.25rem 1.5rem; text-decoration: none; color: #4a5568; font-weight: 600; transition: all 0.3s ease; border-radius: 8px;"
                       onmouseover="this.style.color='#2563eb'; this.style.background='rgba(37, 99, 235, 0.1)'"
                       onmouseout="this.style.color='#4a5568'; this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                        </svg>
                        Empréstimos
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </a>
                    <ul style="position: absolute; top: 100%; left: 0; background: white; min-width: 220px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); border-radius: 12px; padding: 0.5rem 0; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; border: 1px solid rgba(0, 0, 0, 0.1);">
                        <li><a href="novo_emprestimo.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Novo Empréstimo</a></li>
                        <li><a href="listar_emprestimos.php" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; text-decoration: none; color: #4a5568; transition: all 0.2s ease;"
                               onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'; this.style.color='#2563eb'"
                               onmouseout="this.style.background='transparent'; this.style.color='#4a5568'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11H5a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                                <circle cx="9" cy="9" r="2"></circle>
                                <path d="M21 15V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11"></path>
                            </svg>
                            Listar Empréstimos</a></li>
                    </ul>
                </li>

                <!-- Relatórios -->
                <li style="position: relative;">
                    <a href="relatorios.php" style="display: flex; align-items: center; gap: 0.5rem; padding: 1.25rem 1.5rem; text-decoration: none; color: #4a5568; font-weight: 600; transition: all 0.3s ease; border-radius: 8px;"
                       onmouseover="this.style.color='#2563eb'; this.style.background='rgba(37, 99, 235, 0.1)'"
                       onmouseout="this.style.color='#4a5568'; this.style.background='transparent'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        Relatórios
                    </a>
                </li>
            </ul>

            <!-- User Menu -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="position: relative;">
                    <button style="display: flex; align-items: center; gap: 0.5rem; background: none; border: none; padding: 0.5rem; border-radius: 50px; cursor: pointer; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(37, 99, 235, 0.1)'"
                            onmouseout="this.style.background='transparent'">
                        <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb, #1e40af); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                            A
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Container principal com design moderno e responsivo -->
    <div style="max-width: 600px; margin: 0 auto; padding: 2rem 1rem;">
        <!-- Cabeçalho com ícone e título elegante -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="background: white; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                    <path d=
