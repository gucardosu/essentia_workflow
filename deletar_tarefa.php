<?php 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

//verifica se o ID foi enviado pela URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    //deleta o id da tarefa se pertencer ao user logado
    $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
}

header("Location: paine.php");
exit;
?>