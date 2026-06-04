<?php 
require_once 'conexao.php';

$erro = '';
$sucesso = '';

// Processamento do formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpa os dados de entrada
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Valida campos vazios
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Verifica se o e-mail já existe no banco
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $erro = "E-mail já cadastrado!";
            } else {
                // Gera o hash seguro da senha
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // Grava o novo usuário no banco
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $email, $senhaHash]);

                $sucesso = "Usuário cadastrado com sucesso! Você já pode fazer login.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Workflow</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f9; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: white; 
            padding: 2rem; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px; 
        }
        .form-group { 
            margin-bottom: 1rem; 
        }
        input { 
            width: 100%; 
            padding: 8px; 
            margin-top: 5px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 10px; 
            background-color: #0056b3; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        button:hover { 
            background-color: #004494; 
        }
        .erro { 
            color: red; 
            margin-bottom: 1rem; 
        }
        .sucesso { 
            color: green; 
            margin-bottom: 1rem; 
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Criar Conta</h2>

    <?php if ($erro): ?> 
        <div class="erro"><?= $erro ?></div> 
    <?php endif; ?>
    
    <?php if ($sucesso): ?> 
        <div class="sucesso"><?= $sucesso ?></div> 
    <?php endif; ?>

    <form method="POST" action="cadastro.php">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" required>
        </div>
        
        <div class="form-group">
            <label>E-mail:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Senha:</label>
            <input type="password" name="senha" required>
        </div>
        
        <button type="submit">Cadastrar</button>
    </form>
</div>

</body>
</html>