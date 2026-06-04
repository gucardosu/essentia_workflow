<?php
session_start();
require_once __DIR__ . '/conexao.php';

// Proteção de rota
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Lógica de Filtros e Busca
$filtro_status = $_GET['status'] ?? '';
$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM tarefas WHERE usuario_id = ? AND deletado_em IS NULL";
$params = [$usuario_id];

// Adiciona o filtro de status se o usuário selecionou algum
if ($filtro_status) {
    $sql .= " AND status = ?";
    $params[] = $filtro_status;
}

// Adiciona a busca por título se o usuário digitou algo
if ($busca) {
    $sql .= " AND titulo LIKE ?";
    $params[] = "%$busca%"; 
}

$sql .= " ORDER BY criado_em DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar tarefas: " . $e->getMessage());
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
        
        .btn-novo { display: inline-block; background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-bottom: 20px; font-weight: bold; transition: background 0.3s; }
        .btn-novo:hover { background-color: #218838; }
        
        .filtro-form { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); align-items: center; }
        .filtro-form input, .filtro-form select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .filtro-form button { padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .filtro-form button:hover { background-color: #0056b3; }
        .filtro-form a { color: #555; text-decoration: none; padding: 8px 10px; }
        .mensagem { padding: 10px 15px; margin-bottom: 20px; border-radius: 4px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        
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

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="mensagem">
            <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
        </div>
    <?php endif; ?>

    <form method="GET" class="filtro-form">
        <input type="text" name="busca" placeholder="Buscar por título..." value="<?= htmlspecialchars($busca) ?>">
        
        <select name="status">
            <option value="">Todos os status</option>
            <option value="pendente" <?= $filtro_status == 'pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="andamento" <?= $filtro_status == 'andamento' ? 'selected' : '' ?>>Em Andamento</option>
            <option value="concluido" <?= $filtro_status == 'concluido' ? 'selected' : '' ?>>Concluído</option>
        </select>
        
        <button type="submit">Filtrar</button>
        <a href="painel.php">Limpar</a>
    </form>

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
                    <td colspan="4" class="vazio">Nenhuma tarefa encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>