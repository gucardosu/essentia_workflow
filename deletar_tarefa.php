<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (isset($_GET['id']) && isset($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("UPDATE tarefas SET deletado_em = NOW() WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['usuario_id']]);
    
    $_SESSION['mensagem'] = "Tarefa movida para a lixeira!";
}
header("Location: painel.php");
exit;