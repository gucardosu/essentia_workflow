<?php
session_start();
require_once 'conexao.php';

// Proteção de rota
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

//Processa o UPDATE ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tarefas SET titulo = ?, descricao = ?, status = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $descricao, $status, $id, $usuario_id]);
    
    header("Location: painel.php");
    exit;
}

//Busca os dados atuais para preencher o formulário
$stmt = $pdo->prepare("SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $usuario_id]);
$tarefa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarefa) {
    die("Tarefa não encontrada ou sem permissão.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarefa - Workflow</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; font-size: 16px; }
        button:hover { background-color: #0069d9; }
        .btn-voltar { display: block; text-align: center; margin-top: 15px; color: #555; text-decoration: none; }
        .btn-voltar:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h2>Editar Tarefa</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($tarefa['titulo']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4"><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="pendente" <?= $tarefa['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="andamento" <?= $tarefa['status'] == 'andamento' ? 'selected' : '' ?>>Em Andamento</option>
                <option value="concluido" <?= $tarefa['status'] == 'concluido' ? 'selected' : '' ?>>Concluído</option>
            </select>
        </div>
        
        <button type="submit">Salvar Alterações</button>
    </form>
    
    <a href="painel.php" class="btn-voltar">Voltar ao Painel</a>
</div>

</body>
</html>