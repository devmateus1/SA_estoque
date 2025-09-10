<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de adm 
if ($_SESSION['perfil']!= 1) {
    echo"<script>alert('Acesso negado.');window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_fornecedor = $_POST['id_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $nova_senha = !empty($_POST['senha']) ? password_hash($_POST['nova_senha'], PASSWORD_DEFAULT) : null;

// Atualiza os dados do usuario
if ($nova_senha){
    $sql = "UPDATE fornecedor set nome_fornecedor = :nome_fornecedor, endereco = :endereco, telefone = :telefone, email = :email, senha = :senha WHERE id_fornecedor = :id_fornecedor";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $nova_senha);
}else {
    $sql = "UPDATE fornecedor set nome_fornecedor = :nome_fornecedor,  endereco = :endereco, telefone = :telefone, email = :email WHERE id_fornecedor = :id_fornecedor";
    $stmt = $pdo->prepare($sql);
}

$stmt->bindParam(':id_fornecedor', $id_fornecedor);
$stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
$stmt->bindParam(':endereco', $endereco);
$stmt->bindParam(':telefone', $telefone);
$stmt->bindParam(':email', $email);

if ($stmt->execute()){
    echo"<script>alert('Usuário atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
}else {
    echo"<script>alert('Erro ao atualizar o usuário.');window.location.href='alterar_funcionario.php?id=$fornecedor';</script>";
}
}
?>