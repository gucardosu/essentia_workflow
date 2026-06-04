<?php 
session_start();

// Proteção de rota
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$erro = '';

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $status = $_POST['status'];
    
    // Captura o ID do dono da tarefa
    $usuario_id = $_SESSION['usuario_id'];

    if (empty($titulo)) {
        $erro = "O Título é obrigatório.";
    } else {
        try {
            // Insere no banco de dados
            $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, titulo, descricao, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $titulo, $descricao, $status]);
            
            // Redireciona com sucesso
            header("Location: painel.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao salvar a tarefa: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Tarefa - Workflow</title>
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
            max-width: 500px; 
        }
        .form-group { 
            margin-bottom: 1rem; 
        }
        label { 
            display: block; 
            font-weight: bold; 
            margin-bottom: 5px; 
        }
        input[type="text"], textarea, select { 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        button { 
            width: 100%; 
            padding: 10px; 
            background-color: #28a745; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-top: 10px; 
            font-size: 16px; 
        }
        button:hover { 
            background-color: #218838; 
        }
        .btn-voltar { 
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            color: #555; 
            text-decoration: none; 
        }
        .btn-voltar:hover { 
            text-decoration: underline; 
        }
        .erro { 
            color: red; 
            margin-bottom: 1rem; 
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Criar Nova Tarefa</h2>

    <?php if ($erro): ?> 
        <div class="erro"><?= $erro ?></div> 
    <?php endif; ?>

    <form method="POST" action="nova_tarefa.php">
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="titulo" required>
        </div>
        
        <div class="form-group">
            <label>Descrição (Opcional):</label>
            <textarea name="descricao" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="pendente">Pendente</option>
                <option value="andamento">Em Andamento</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>

        <button type="submit">Salvar Tarefa</button>
    </form>
    
    <a href="painel.php" class="btn-voltar">Voltar ao Painel</a>
</div>

</body>
</html>