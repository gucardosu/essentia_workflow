<?php 
session_start();

require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os dados.";
    } else {
        try {
            //Busca o user pelo email
            $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch((PDO::FETCH_ASSOC)); //pega os dados como um array associativo

            //Verifica se o user existe e se a senha bate com o hash do db
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                //Deu certo, salva os dados
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                //Redireciona para a pagina interna
                header("Location: painel.php");
                exit; //Lembrar de colocar o exit sempre apos um header redirect
            } else {
                $erro = "E-mail ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no bando de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Workflow</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 1rem; }
        input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 10px; }
        button:hover { background-color: #004494; }
        .erro { color: red; margin-bottom: 1rem; text-align: center; }
        .link-cadastro { display: block; text-align: center; font-size: 0.9em; color: #555; text-decoration: none; }
        .link-cadastro:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h2>Acessar Sistema</h2>

    <?php if ($erro): ?> <div class="erro"><?= $erro ?></div> <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label>E-mail:</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Senha:</label>
            <input type="password" name="senha" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
    
    <a href="cadastro.php" class="link-cadastro">Ainda não tem conta? Cadastre-se</a>
</div>

</body>
</html>

