<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../inc/conf/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$tarefa_id = (int)($_POST['tarefa_id'] ?? 0);
$coluna    = $_POST['coluna'] ?? '';

$validas = ['backlog','andamento','revisao','concluido'];
if ($tarefa_id <= 0 || !in_array($coluna, $validas, true)) {
    http_response_code(400);
    exit;
}

$stmt = $pdo->prepare("
  UPDATE projeto_tarefas
  SET coluna = ?, atualizado_em = NOW()
  WHERE id = ?
");
$stmt->execute([$coluna, $tarefa_id]);

http_response_code(204); // sem conteúdo
