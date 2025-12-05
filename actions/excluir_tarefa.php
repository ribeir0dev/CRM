<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /modules/painel.php?mod=projetos');
    exit;
}

$tarefa_id  = (int)($_POST['tarefa_id'] ?? 0);
$projeto_id = (int)($_POST['projeto_id'] ?? 0);

if ($tarefa_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM projeto_tarefas WHERE id = ?");
    $stmt->execute([$tarefa_id]);
}

header('Location: /modules/painel.php?mod=projeto_detalhe&id=' . $projeto_id);
exit;
