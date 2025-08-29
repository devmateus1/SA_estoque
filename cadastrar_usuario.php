<?php
// Processar formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Validações básicas
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "Nome é obrigatório";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email válido é obrigatório";
    }
    
    if (empty($senha) || strlen($senha) < 6) {
        $erros[] = "Senha deve ter pelo menos 6 caracteres";
    }
    
    if ($senha !== $confirmar_senha) {
        $erros[] = "Senhas não coincidem";
    }
    
    // Se não há erros, processar cadastro
    if (empty($erros)) {
        // Aqui você adicionaria a lógica para salvar no banco de dados
        $sucesso = "Usuário cadastrado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
</head>
<body style="
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #1a4d3a 0%, #2d5a3d 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2c2c2c;
">
    <div style="
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        margin: 1rem;
    ">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="
                color: #1a4d3a;
                margin: 0 0 0.5rem 0;
                font-size: 1.8rem;
                font-weight: 600;
            ">Criar Conta</h1>
            <p style="
                color: #666;
                margin: 0;
                font-size: 0.9rem;
            ">Preencha os dados para se cadastrar</p>
        </div>

        <?php if (!empty($erros)): ?>
            <div style="
                background: #fee;
                border: 1px solid #fcc;
                color: #c33;
                padding: 1rem;
                border-radius: 6px;
                margin-bottom: 1rem;
                font-size: 0.9rem;
            ">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div style="
                background: #efe;
                border: 1px solid #cfc;
                color: #363;
                padding: 1rem;
                border-radius: 6px;
                margin-bottom: 1rem;
                font-size: 0.9rem;
                text-align: center;
            ">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label for="nome" style="
                    display: block;
                    margin-bottom: 0.5rem;
                    color: #1a4d3a;
                    font-weight: 500;
                    font-size: 0.9rem;
                ">Nome Completo</label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 0.75rem;
                        border: 2px solid #e0e0e0;
                        border-radius: 6px;
                        font-size: 1rem;
                        transition: all 0.3s ease;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#3a6b4a'; this.style.outline='none';"
                    onblur="this.style.borderColor='#e0e0e0';"
                >
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="
                    display: block;
                    margin-bottom: 0.5rem;
                    color: #1a4d3a;
                    font-weight: 500;
                    font-size: 0.9rem;
                ">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 0.75rem;
                        border: 2px solid #e0e0e0;
                        border-radius: 6px;
                        font-size: 1rem;
                        transition: all 0.3s ease;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#3a6b4a'; this.style.outline='none';"
                    onblur="this.style.borderColor='#e0e0e0';"
                >
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="senha" style="
                    display: block;
                    margin-bottom: 0.5rem;
                    color: #1a4d3a;
                    font-weight: 500;
                    font-size: 0.9rem;
                ">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    required
                    minlength="6"
                    style="
                        width: 100%;
                        padding: 0.75rem;
                        border: 2px solid #e0e0e0;
                        border-radius: 6px;
                        font-size: 1rem;
                        transition: all 0.3s ease;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#3a6b4a'; this.style.outline='none';"
                    onblur="this.style.borderColor='#e0e0e0';"
                >
            </div>

            <div style="margin-bottom: 2rem;">
                <label for="confirmar_senha" style="
                    display: block;
                    margin-bottom: 0.5rem;
                    color: #1a4d3a;
                    font-weight: 500;
                    font-size: 0.9rem;
                ">Confirmar Senha</label>
                <input 
                    type="password" 
                    id="confirmar_senha" 
                    name="confirmar_senha" 
                    required
                    minlength="6"
                    style="
                        width: 100%;
                        padding: 0.75rem;
                        border: 2px solid #e0e0e0;
                        border-radius: 6px;
                        font-size: 1rem;
                        transition: all 0.3s ease;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#3a6b4a'; this.style.outline='none';"
                    onblur="this.style.borderColor='#e0e0e0';"
                >
            </div>

            <button 
                type="submit"
                style="
                    width: 100%;
                    background: linear-gradient(135deg, #2d5a3d 0%, #3a6b4a 100%);
                    color: white;
                    padding: 0.875rem;
                    border: none;
                    border-radius: 6px;
                    font-size: 1rem;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    margin-bottom: 1rem;
                "
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(45, 90, 61, 0.3)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
            >
                Criar Conta
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="
                color: #666;
                font-size: 0.9rem;
                margin: 0;
            ">
                Já tem uma conta? 
                <a href="index.php" style="
                    color: #3a6b4a;
                    text-decoration: none;
                    font-weight: 500;
                ">Fazer login</a>
            </p>
        </div>
    </div>

    <script>
        // Validação em tempo real para confirmar senha
        document.getElementById('confirmar_senha').addEventListener('input', function() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = this.value;
            
            if (confirmarSenha && senha !== confirmarSenha) {
                this.style.borderColor = '#e74c3c';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    </script>
</body>
</html>
