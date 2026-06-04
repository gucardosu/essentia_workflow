<?php 
session_start();

//Proteção de rota
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$tarefas = [];

try {
    //Busca apenas as tarefas do user logado, da mais recente a mais antiga
    $stmt = $pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = ? ORDER BY criado_em DESC");
    $stmt->execute([$usuario_id]);
    $tarefas = $stmt->fetchALL(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar tarefas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel - Workflow</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn-sair { background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .btn-sair:hover { background-color: #c82333; }
        .btn-novo { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; }
        .btn-novo:hover { background-color: #218838; }
        
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0056b3; color: white; }
        .status-pendente { color: #856404; background-color: #fff3cd; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-andamento { color: #004085; background-color: #cce5ff; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-concluido { color: #155724; background-color: #d4edda; padding: 3px 8px; border-radius: 4px; font-size: 0.9em; }
        .vazio { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Bem-vindo(a), <?= htmlspecialchars($usuario_nome) ?>!</h2>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>

    <a href="nova_tarefa.php" class="btn-novo">+ Nova Tarefa</a>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Status</th>
                <th>Data de Criação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tarefas) > 0): ?>
                <?php foreach ($tarefas as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['titulo']) ?></td>
                        <td>
                            <span class="status-<?= $t['status'] ?>">
                                <?= ucfirst($t['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($t['criado_em'])) ?></td>
                        <td>
                            <a href="editar_tarefa.php?id=<?= $t['id'] ?>">Editar</a> | 
                            <a href="deletar_tarefa.php?id=<?= $t['id'] ?>" style="color: red;" onclick="return confirm('Tem certeza que deseja deletar?');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="vazio">Nenhuma tarefa encontrada. Comece criando uma!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>